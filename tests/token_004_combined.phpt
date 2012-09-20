--TEST--
Basic test: three consecutive literals
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\Token;
use hatchet\tokens\Literal;

class TestGrammar4 extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Token('', array(
			new Literal('char', 'a'),
			new Literal('char', ':'),
			new Literal('char', 'b'),
		));
	}
}

$grammar = new TestGrammar4();
dump_tree($grammar->parse('a:b'));

?>
--EXPECT--
'' text: 'a:b'
	'char' text: 'a'
	'char' text: ':'
	'char' text: 'b'
