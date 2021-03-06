--TEST--
Basic test: three consecutive literals
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\Token;
use Hatchet\Tokens\Literal;

class TestGrammar4 extends Grammar
{
	public function __construct()
	{
		$this->rootToken = new Token('', array(
			new Literal('char', 'a'),
			new Literal('char', ':'),
			new Literal('char', 'b'),
		));
	}
}

$grammar = new TestGrammar4();
dumpTree($grammar->parse('a:b'));

?>
--EXPECT--
'' text: 'a:b'
	'char' text: 'a'
	'char' text: ':'
	'char' text: 'b'
