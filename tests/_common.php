<?php

// we don't have a namespace here to unclutter the tests

error_reporting(E_ALL);

require_once __DIR__ . '/../lib/autoload.php';

function dumpTree($nodes, $level = 0)
{
    foreach ($nodes as $node) {
        $prefix = str_repeat("\t", $level);
        echo $prefix . var_export($node['name'], true) . ' text: ' . var_export($node['text'], true) . "\n";
        dumpTree($node['childNodes'], $level + 1);
    }
}

function dumpTreeLessNoise($nodes, $level = 0)
{
    foreach ($nodes as $node) {
        $prefix = str_repeat("\t", $level);
        echo $prefix . ($node['name'] ? : '{root}')
            . (empty($node['childNodes']) ? ' '
            . strtr($node['text'], array("\n" => "\\n", "\t" => "\\t")) : '') . "\n"
        ;
        dumpTreeLessNoise($node['childNodes'], $level + 1);
    }
}

function testCalc($expr)
{
    $parser = new \hatchet\Grammar(file_get_contents(__DIR__ . '/calc.hatchet'));
    echo "\nExpression: $expr\n";
    try {
        $tree = $parser->parse($expr);
        echo "Result: " . compileCalcTree($tree) . "\n";
    } catch (\hatchet\Exception $e) {
        echo 'Exception: ' . $e->getMessage() . "\n";
    }
}

function compileCalcTree($tree)
{
    $ans = 0;
    $op  = '+';
    foreach ($tree as $node) {
        switch ($node['name']) {
            case 'plus':
            case 'times':
                $op = $node['text'];
                break;

            case 'number':
                $ans = eval("return {$ans} {$op} {$node['text']};");
                break;

            default:
                $subtreeResult = compileCalcTree($node['childNodes']);
                $ans = eval("return {$ans} {$op} {$subtreeResult};");
                break;
        }
    }
    return $ans;
}
