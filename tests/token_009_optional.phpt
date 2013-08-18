--TEST--
Basic test: optional tokens
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\Token;
use Hatchet\Tokens\Multiplier;
use Hatchet\Tokens\Literal;

class TestGrammar9 extends Grammar
{
	public function __construct()
	{
		$this->rootToken = new Token('', array(
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
