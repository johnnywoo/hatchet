--TEST--
Basic test: flattening of anonymous tokens
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\Token;
use hatchet\tokens\Multiplier;
use hatchet\tokens\Literal;

class TestGrammar extends Grammar
{
	public function __construct()
	{
		$this->root_token = new Token('', array(
			new Literal('start', '>'),
			// the multiplier is anonymous, so its children will be present instead of it
			new Multiplier(null, array(
				new Literal('letter', 'a'),
				new Literal('number', '1'),
				new Multiplier(null, array(
					// this token is anonymous and will not be included in the tree
					new Literal(null, ','),
				)),
			)),
			new Literal('end', '<'),
		));
	}
}

$grammar = new TestGrammar();
dump_tree($grammar->parse('>a1,a1<'));

?>
--EXPECT--
'' text: '>a1,a1<'
	'start' text: '>'
	'letter' text: 'a'
	'number' text: '1'
	'letter' text: 'a'
	'number' text: '1'
	'end' text: '<'