--TEST--
Basic test: optional tokens
--ARGS--
--FILE--
<?php

require_once '_common.php';

use hatchet\Grammar;
use hatchet\tokens\Token;
use hatchet\tokens\Multiplier;
use hatchet\tokens\Literal;

class TestGrammar9 extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Token('', array(
			new Multiplier('', array(new Literal('', 'a')), true),
			new Literal('', 'b'),
		));
	}
}

$grammar = new TestGrammar9();

echo "Normal\n";
dumpTree($grammar->parse('ab'));

echo "\nEmpty\n";
dumpTree($grammar->parse('b'));

?>
--EXPECT--
Normal
'' text: 'ab'
	'' text: 'a'
		'' text: 'a'
	'' text: 'b'

Empty
'' text: 'b'
	'' text: 'b'
