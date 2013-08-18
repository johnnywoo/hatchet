--TEST--
Our own grammar: basic
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\HatchetGrammar;

$grammar = new HatchetGrammar();
dumpTree($grammar->parse(':"a"'));

?>
--EXPECT--
'DEFINITION' text: ':"a"'
	'BODY' text: '"a"'
		'LITERAL' text: '"a"'
