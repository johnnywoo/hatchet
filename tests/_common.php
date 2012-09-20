<?

error_reporting(E_ALL);

require_once __DIR__.'/../lib/autoload.php';

function dump_tree($nodes, $level = 0)
{
	foreach($nodes as $node)
	{
		$prefix = str_repeat("\t", $level);
		echo $prefix . var_export($node['name'], true).' text: ' . var_export($node['text'], true)."\n";
		dump_tree($node['child_nodes'], $level + 1);
	}
}

function dump_tree_less_noise($nodes, $level = 0)
{
	foreach($nodes as $node)
	{
		$prefix = str_repeat("\t", $level);
		echo $prefix . ($node['name'] ?: '{root}') . (empty($node['child_nodes']) ? ' '.strtr($node['text'], array("\n" => "\\n", "\t" => "\\t")) : '') . "\n";
		dump_tree_less_noise($node['child_nodes'], $level + 1);
	}
}

function test_calc($expr)
{
	$parser = new \hatchet\Grammar(file_get_contents(__DIR__.'/calc.hatchet'));
	echo "\nExpression: $expr\n";
	try
	{
		$tree = $parser->parse($expr);
		echo "Result: " . compile_calc_tree($tree) . "\n";
	}
	catch(\hatchet\Exception $e)
	{
		echo 'Exception: '.$e->getMessage() . "\n";
		return;
	}
//	dump_tree_less_noise($tree);
}

function compile_calc_tree($tree)
{
	$ans = 0;
	$op = '+';
	foreach($tree as $node)
	{
		switch($node['name'])
		{
			case 'plus':
			case 'times':
				$op = $node['text'];
				break;

			case 'number':
				$ans = eval("return $ans $op ".$node['text'].';');
				break;

			default:
				$ans = eval("return $ans $op ".compile_calc_tree($node['child_nodes']).';');
				break;
		}
	}
	return $ans;
}