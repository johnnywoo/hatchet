--TEST--
Basic test: three consecutive literals
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\Token;
use hatchet\tokens\Literal;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Token('', array(
			new Literal('', 'a'),
			new Literal('', ':'),
			new Literal('', 'b'),
		));
	}
}

$grammar = new TestGrammar();
dump_tree($grammar->parse('a:b'));

?>
--EXPECT--
name: '' text: 'a:b'
	name: '' text: 'a'
	name: '' text: ':'
	name: '' text: 'b'