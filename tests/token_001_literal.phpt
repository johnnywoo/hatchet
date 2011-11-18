--TEST--
Basic test: literal
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\Literal;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Literal('', 'a');
	}
}

$grammar = new TestGrammar();
dump_tree($grammar->parse('a'));

?>
--EXPECT--
name: '' text: 'a'