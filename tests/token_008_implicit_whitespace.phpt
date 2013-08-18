--TEST--
Basic test: whitespace around tokens should be ignored
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\Multiplier;
use Hatchet\Tokens\Literal;

class TestGrammar8 extends Grammar
{
	public function __construct()
	{
		$this->rootToken = new Multiplier('', array(
			new Literal('char', 'a'),
		));
	}
}

$grammar = new TestGrammar8();
dumpTree($grammar->parse(" a  \taa "));

?>
--EXPECT--
'' text: ' a  	aa'
	'char' text: 'a'
	'char' text: 'a'
	'char' text: 'a'
