--TEST--
Basic test: whole grammar is one simple quoted string
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\QuotedString;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new QuotedString('');
	}
}

$grammar = new TestGrammar();
dump_tree($grammar->parse('"kar"'));

?>
--EXPECT--
name: '' text: '"kar"'