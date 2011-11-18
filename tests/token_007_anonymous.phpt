--TEST--
Basic test: flattening of anonymous tokens
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\Token;
use hatchet\hatchet_grammar\Multiplier;
use hatchet\hatchet_grammar\Literal;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Token('', array(
			new Multiplier(null, array(
				new Literal('letter', 'a'),
				new Literal('number', '1'),
			)),
		));
	}
}

$grammar = new TestGrammar();
dump_tree($grammar->parse('a1a1'));

?>
--EXPECT--
name: '' text: 'a1a1'
	name: 'letter' text: 'a'
	name: 'number' text: '1'
	name: 'letter' text: 'a'
	name: 'number' text: '1'