--TEST--
Our own grammar: empty alternatives should be optimized out
--ARGS--
--FILE--
<?php

require_once '_common.php';

use hatchet\HatchetGrammar;

$grammar = new HatchetGrammar();
dumpTree($grammar->parse('	: [ "head" ] "body" [ "tail" ] '));

/**
 * The syntax for optional token is: "[" alternative "]"
 * Therefore without optimization every optional token would look like this:
 * CONDITION -> ALTERNATIVE-TOKENS -> TOKENS -> (child nodes)
 * The optimization makes it look like CONDITION -> (child nodes).
 */

?>
--EXPECT--
'DEFINITION' text: '	: [ "head" ] "body" [ "tail" ]'
	'BODY' text: ' [ "head" ] "body" [ "tail" ]'
		'CONDITION' text: ' [ "head" ]'
			'LITERAL' text: '"head"'
		'LITERAL' text: '"body"'
		'CONDITION' text: '[ "tail" ]'
			'LITERAL' text: '"tail"'
