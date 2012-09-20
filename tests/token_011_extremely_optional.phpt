--TEST--
Basic test: very optional tokens; multiplier should not hang
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\Token;
use hatchet\tokens\Multiplier;
use hatchet\tokens\Literal;

function optional($token)
{
	return new Multiplier('', array($token), true);
}

class TestGrammar11 extends Grammar
{
	public function __construct()
	{
		// : { [["a"] "b"] } "c"
		$this->root_token = new Token('', array(
			new Multiplier(null, array(
				new Multiplier(null, array(
					new Multiplier(null, array(
						new Literal('a', 'a'),
					), true),
					new Literal('b', 'b'),
				), true),
			)),
			new Literal('c', 'c'),
		));
	}
}

$grammar = new TestGrammar11();

echo "Empty\n";
dump_tree($grammar->parse('c'));

echo "\nMedium\n";
dump_tree($grammar->parse('b c'));

echo "\nNormal\n";
dump_tree($grammar->parse('ab c'));

echo "\nMixed\n";
dump_tree($grammar->parse('ab b ab c'));

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
