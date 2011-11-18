--TEST--
Basic test: alternative
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\hatchet_grammar\Alternative;
use hatchet\hatchet_grammar\Literal;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Alternative('', array(
			new Literal('', 'a'),
			new Literal('', 'b'),
		));
	}
}

$grammar = new TestGrammar();

echo "First\n";
dump_tree($grammar->parse('a'));

echo "\nSecond\n";
dump_tree($grammar->parse('b'));

?>
--EXPECT--
First
name: '' text: 'a'
	name: '' text: 'a'

Second
name: '' text: 'b'
	name: '' text: 'b'