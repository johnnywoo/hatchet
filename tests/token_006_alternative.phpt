--TEST--
Basic test: alternative
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\Alternative;
use Hatchet\Tokens\Literal;

class TestGrammar6 extends Grammar
{
	public function __construct()
	{
		$this->rootToken = new Alternative('', array(
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
