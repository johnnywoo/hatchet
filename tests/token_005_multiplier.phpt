--TEST--
Basic test: multiplier
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\Multiplier;
use hatchet\tokens\Literal;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Multiplier('', array(
			new Literal('', 'a'),
		));
	}
}

$grammar = new TestGrammar();

echo "Normal\n";
dump_tree($grammar->parse('aaa'));

echo "\nEmpty\n";
dump_tree($grammar->parse(''));

?>
--EXPECT--
Normal
name: '' text: 'aaa'
	name: '' text: 'a'
	name: '' text: 'a'
	name: '' text: 'a'

Empty
name: '' text: ''
