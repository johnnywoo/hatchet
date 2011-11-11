--TEST--
Basic test: whole grammar is one literal
--ARGS--
--FILE--
<?php

require_once __DIR__.'/../lib/autoload.php';
use hatchet\Grammar;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new hatchet\hatchet_grammar\Literal('a');
	}
}

$grammar = new TestGrammar();

$node = $grammar->parse('a');
var_dump($node->text, $node->children);

?>
--EXPECT--
string(1) "a"
array(0) {
}