--TEST--
Our own grammar: basic
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\HatchetGrammar;

$grammar = new HatchetGrammar();
dump_tree($grammar->parse(':"a"'));

?>
--EXPECT--
'DEFINITION' text: ':"a"'
	'BODY' text: '"a"'
		'LITERAL' text: '"a"'
