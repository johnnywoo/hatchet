--TEST--
Basic test: very optional tokens; multiplier should not hang
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\Token;
use Hatchet\Tokens\Multiplier;
use Hatchet\Tokens\Literal;

function optional($token)
{
	return new Multiplier('', array($token), true);
}

class TestGrammar11 extends Grammar
{
	public function __construct()
	{
		// : { [["a"] "b"] } "c"
		$this->rootToken = new Token('', array(
			new Multiplier(null, array(
				new Multiplier(
                    null,
                    array(
                        new Multiplier(
                            null,
                            array(new Literal('a', 'a')),
                            true  // only one or zero
                        ),
                        new Literal('b', 'b'),
                    ),
                    true  // only one or zero
                ),
			)),
			new Literal('c', 'c'),
		));
	}
}

$grammar = new TestGrammar11();

echo "Empty\n";
dumpTree($grammar->parse('c'));

echo "\nMedium\n";
dumpTree($grammar->parse('b c'));

echo "\nNormal\n";
dumpTree($grammar->parse('ab c'));

echo "\nMixed\n";
dumpTree($grammar->parse('ab b ab c'));

?>
--EXPECT--
Empty
'' text: 'c'
	'c' text: 'c'

Medium
'' text: 'b c'
	'b' text: 'b'
	'c' text: 'c'

Normal
'' text: 'ab c'
	'a' text: 'a'
	'b' text: 'b'
	'c' text: 'c'

Mixed
'' text: 'ab b ab c'
	'a' text: 'a'
	'b' text: 'b'
	'b' text: 'b'
	'a' text: 'a'
	'b' text: 'b'
	'c' text: 'c'
