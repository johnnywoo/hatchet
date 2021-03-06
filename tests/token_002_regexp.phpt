--TEST--
Basic test: regexp
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\Regexp;

class TestGrammar2 extends Grammar
{
	public function __construct()
	{
		$this->rootToken = new Regexp('', '/a[0-9]/');
	}
}

$grammar = new TestGrammar2();
dumpTree($grammar->parse('a1'));

?>
--EXPECT--
'' text: 'a1'
