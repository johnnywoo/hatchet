--TEST--
Basic test: whole grammar is one simple quoted string
--ARGS--
--FILE--
<?php

require_once __DIR__.'/../lib/autoload.php';
use hatchet\Grammar;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new hatchet\Token_QuotedString();
	}
}

$grammar = new TestGrammar();

$node = $grammar->parse('"kar"');
var_dump($node->text, $node->children);

?>
--EXPECT--
string(5) ""kar""
array(0) {
}