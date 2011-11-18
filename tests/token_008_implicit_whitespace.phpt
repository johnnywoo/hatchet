--TEST--
Basic test: whitespace around tokens should be ignored
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\Token;
use hatchet\tokens\Multiplier;
use hatchet\tokens\Literal;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Multiplier('', array(
			new Literal('char', 'a'),
		));
	}
}

$grammar = new TestGrammar();
dump_tree($grammar->parse(" a  \taa "));

?>
--EXPECT--
name: '' text: ' a  	aa'
	name: 'char' text: 'a'
	name: 'char' text: 'a'
	name: 'char' text: 'a'