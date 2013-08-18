--TEST--
Our own grammar: whitespace declaration should be allowed
--ARGS--
--FILE--
<?php

require_once '_common.php';

use hatchet\HatchetGrammar;

function doit($name, $text)
{
    static $parser = null;
    if (!$parser) {
        $parser = new HatchetGrammar();
    }

    echo "== {$name}\n";
    try {
        dumpTree($parser->parse($text));
    } catch (Exception $e) {
        echo get_class($e) . ': ' . $e->getMessage() . "\n";
    }
}

doit('Manual', '
	@whitespace manual
	:"a"
');
doit('Inline', '
	@whitespace inline
	:"a"
');
doit('Implicit', '
	@whitespace implicit
	:"a"
');
doit('No value', '
	@whitespace
	:"a"
');
doit('Wrong value', '
	@whitespace kekek
	:"a"
');
doit('Wrong case', '
	@whitespace MANUAL
	:"a"
');
doit('Wrong statement case', '
	@WHITESPACE manual
	:"a"
');

?>
--EXPECT--
== Manual
'WHITESPACE-MODE' text: 'manual'
'DEFINITION' text: '	:"a"'
	'BODY' text: '"a"'
		'LITERAL' text: '"a"'
== Inline
'WHITESPACE-MODE' text: 'inline'
'DEFINITION' text: '	:"a"'
	'BODY' text: '"a"'
		'LITERAL' text: '"a"'
== Implicit
'WHITESPACE-MODE' text: 'implicit'
'DEFINITION' text: '	:"a"'
	'BODY' text: '"a"'
		'LITERAL' text: '"a"'
== No value
hatchet\Exception: Parse error: root token does not cover the whole text
== Wrong value
hatchet\Exception: Parse error: root token does not cover the whole text
== Wrong case
hatchet\Exception: Parse error: root token does not cover the whole text
== Wrong statement case
hatchet\Exception: Parse error: root token does not cover the whole text
