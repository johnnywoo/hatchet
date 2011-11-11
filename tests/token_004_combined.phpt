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
		$this->root_token = new hatchet\Token(array(
			new hatchet\hatchet_grammar\Literal('a'),
			new hatchet\hatchet_grammar\Literal(':'),
			new hatchet\hatchet_grammar\Literal('b'),
		));
	}
}

$grammar = new TestGrammar();

$node = $grammar->parse('a:b');

$children = array();
foreach($node->children as $token)
{
	$children[] = $token->text;
}

var_dump($node->text, $children);

?>
--EXPECT--
string(3) "a:b"
array(3) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) ":"
  [2]=>
  string(1) "b"
}