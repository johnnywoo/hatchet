--TEST--
Basic test: whole grammar is one simple quoted string
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\QuotedString;

class TestGrammar3 extends Grammar
{
	public function __construct()
	{
		$this->rootToken = new QuotedString('');
	}
}

$grammar = new TestGrammar3();
dumpTree($grammar->parse('"kar"'));

?>
--EXPECT--
'' text: '"kar"'
