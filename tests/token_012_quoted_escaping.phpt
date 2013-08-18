--TEST--
Basic test: escaping in the quoted strings
--ARGS--
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;
use hatchet\tokens\QuotedString;

class TestGrammar12 extends Grammar
{
	public function __construct()
	{
		$this->root_token = new QuotedString('');
	}
}

$grammar = new TestGrammar12();

// trivial
$nodes = $grammar->parse('"a"');
dump_tree($nodes);
var_export(QuotedString::decode($nodes[0]['text']));
echo "\n\n";

// slash at end
$nodes = $grammar->parse('"\\\\"');
dump_tree($nodes);
var_export(QuotedString::decode($nodes[0]['text']));
echo "\n\n";

// possible chars
$nodes = $grammar->parse('"CR >\\r< LF >\\n< TAB >\\t< SLASH >\\\\< QUOTE >\\"< HEX >\\x45<"');
dump_tree($nodes);
var_export(strtr(QuotedString::decode($nodes[0]['text']), array(
    "\r" => 'ACTUAL_CR_HERE',
    "\n" => 'ACTUAL_LF_HERE',
    "\t" => 'ACTUAL_TAB_HERE',
)));
echo "\n\n";

// 'text' portion there means the text that was scanned from the parsed body
// so it should appear unchanged

?>
--EXPECT--
'' text: '"a"'
'a'

'' text: '"\\\\"'
'\\'

'' text: '"CR >\\r< LF >\\n< TAB >\\t< SLASH >\\\\< QUOTE >\\"< HEX >\\x45<"'
'CR >ACTUAL_CR_HERE< LF >ACTUAL_LF_HERE< TAB >ACTUAL_TAB_HERE< SLASH >\\< QUOTE >"< HEX >E<'
