--TEST--
Basic test: whole grammar is one regexp
--ARGS--
--FILE--
<?php

require_once __DIR__.'/../lib/autoload.php';
use hatchet\Grammar;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new hatchet\hatchet_grammar\Regexp('/a[0-9]/');
	}
}

$grammar = new TestGrammar();

$node = $grammar->parse('a1');
var_dump($node->text, $node->children);

?>
--EXPECT--
string(2) "a1"
array(0) {
}