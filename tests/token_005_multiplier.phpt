--TEST--
Basic test: multiplier
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\Multiplier;
use Hatchet\Tokens\Literal;

class TestGrammar5 extends Grammar
{
	public function __construct()
	{
		$this->rootToken = new Multiplier('', array(
			new Literal('', 'a'),
		));
	}
}

$grammar = new TestGrammar5();

echo "Normal\n";
dumpTree($grammar->parse('aaa'));

echo "\nEmpty\n";
dumpTree($grammar->parse(''));

?>
--EXPECT--
Normal
'' text: 'aaa'
	'' text: 'a'
	'' text: 'a'
	'' text: 'a'

Empty
