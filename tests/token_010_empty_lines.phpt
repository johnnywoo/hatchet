--TEST--
Basic test: a multiplier with empty tokens
--ARGS--
--FILE--
<?php

require_once '_common.php';

use hatchet\Grammar;
use hatchet\tokens\Token;
use hatchet\tokens\Multiplier;
use hatchet\tokens\Literal;

class TestGrammar10 extends Grammar
{
	public function __construct()
	{
		// grammar is like this
		// : [line {"-" line}]
		// line: ["a"]
		// This should mean any number of dashes with "a" on some of them.
		// The intended newline symbol is of course "\n",
		// but the dump becomes quite unreadable with it.
		$line = new Multiplier(
            'line',
            array(new Literal('word', 'a')),
            true // only one or zero
        );
		$this->rootToken = new Multiplier(
            '',
            array(
                $line,
                new Multiplier(null, array(
                    new Literal('newline', "-"),
                    $line,
                )),
            ),
            true // only one or zero
        );
	}
}

$grammar = new TestGrammar10();

echo "Empty\n";
dumpTree($grammar->parse(''));

echo "\nFilled\n";
dumpTree($grammar->parse('a-a'));

echo "\nEmpty lines\n";
dumpTree($grammar->parse('--'));

echo "\nMixed\n";
dumpTree($grammar->parse('a--a-'));

?>
--EXPECT--
Empty

Filled
'' text: 'a-a'
	'line' text: 'a'
		'word' text: 'a'
	'newline' text: '-'
	'line' text: 'a'
		'word' text: 'a'

Empty lines
'' text: '--'
	'newline' text: '-'
	'newline' text: '-'

Mixed
'' text: 'a--a-'
	'line' text: 'a'
		'word' text: 'a'
	'newline' text: '-'
	'newline' text: '-'
	'line' text: 'a'
		'word' text: 'a'
	'newline' text: '-'
