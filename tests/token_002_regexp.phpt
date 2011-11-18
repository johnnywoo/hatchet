--TEST--
Basic test: regexp
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\hatchet_grammar\Regexp;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Regexp('', '/a[0-9]/');
	}
}

$grammar = new TestGrammar();
dump_tree($grammar->parse('a1'));

?>
--EXPECT--
name: '' text: 'a1'