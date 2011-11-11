--TEST--
Basic test: whole grammar is one simple quoted string
--ARGS--
--FILE--
<?php

require_once __DIR__.'/../lib/autoload.php';
use hatchet\Grammar;

$grammar = new Grammar(':"a"');

$node = $grammar->parse('a');
var_dump($node->text, $node->children);

?>
--EXPECT--
string(1) "a"
array(0) {
}