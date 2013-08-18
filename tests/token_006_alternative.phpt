--TEST--
Basic test: alternative
--ARGS--
--FILE--
<?php

require_once '_common.php';

use hatchet\Grammar;
use hatchet\tokens\Alternative;
use hatchet\tokens\Literal;

class TestGrammar6 extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Alternative('', array(
			new Literal('', 'a'),
			new Literal('', 'b'),
		));
	}
}

$grammar = new TestGrammar6();

echo "First\n";
dumpTree($grammar->parse('a'));

echo "\nSecond\n";
dumpTree($grammar->parse('b'));

?>
--EXPECT--
First
'' text: 'a'
	'' text: 'a'

Second
'' text: 'b'
	'' text: 'b'
