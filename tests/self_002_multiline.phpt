--TEST--
Our own grammar: multiline spec
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\HatchetGrammar;

$grammar = new HatchetGrammar();
dumpTree($grammar->parse('
	: body
	body: "a"
'));

?>
--EXPECT--
'DEFINITION' text: '	: body'
	'BODY' text: ' body'
		'NAME' text: 'body'
'DEFINITION' text: '	body: "a"'
	'NAME' text: 'body'
	'BODY' text: ' "a"'
		'LITERAL' text: '"a"'
