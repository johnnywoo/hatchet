--TEST--
Our own grammar: nested alternatives
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\HatchetGrammar;

$grammar = new HatchetGrammar();
dumpTree($grammar->parse('	: ( ("a" | "A") | ("x" | "x") ) '));

?>
--EXPECT--
'DEFINITION' text: '	: ( ("a" | "A") | ("x" | "x") )'
	'BODY' text: ' ( ("a" | "A") | ("x" | "x") )'
		'ALTERNATIVE-TOKENS' text: ' ("a" | "A") | ("x" | "x")'
			'ALTERNATIVE-TOKENS' text: '"a" | "A"'
				'LITERAL' text: '"a"'
				'LITERAL' text: '"A"'
			'ALTERNATIVE-TOKENS' text: '"x" | "x"'
				'LITERAL' text: '"x"'
				'LITERAL' text: '"x"'
