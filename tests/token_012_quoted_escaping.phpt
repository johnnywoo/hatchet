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

function dump_tree_and_dequote($string)
{
    $grammar = new TestGrammar12();
    $nodes = $grammar->parse($string);
    dumpTree($nodes);
    var_export(strtr(QuotedString::decode($nodes[0]['text']), array(
        "\r" => 'ACTUAL_CR_HERE',
        "\n" => 'ACTUAL_LF_HERE',
        "\t" => 'ACTUAL_TAB_HERE',
    )));
    echo "\n\n";
}

// trivial
dump_tree_and_dequote('"a"');

// slash at end
dump_tree_and_dequote('"\\\\"');

// quote at end
dump_tree_and_dequote('"\\""');

// invalid escapes (should remain as is)
dump_tree_and_dequote('"\\z \\xZZ"');

// possible chars
dump_tree_and_dequote('"CR >\\r< LF >\\n< TAB >\\t< SLASH >\\\\< QUOTE >\\"< HEX >\\x45<"');

// possible chars with single quotes
dump_tree_and_dequote("'CR >\\r< LF >\\n< TAB >\\t< SLASH >\\\\< QUOTE >\\'< HEX >\\x45<'");

// 'text' portion there means the text that was scanned from the parsed body
// so it should appear unchanged

?>
--EXPECT--
'' text: '"a"'
'a'

'' text: '"\\\\"'
'\\'

'' text: '"\\""'
'"'

'' text: '"\\z \\xZZ"'
'\\z \\xZZ'

'' text: '"CR >\\r< LF >\\n< TAB >\\t< SLASH >\\\\< QUOTE >\\"< HEX >\\x45<"'
'CR >ACTUAL_CR_HERE< LF >ACTUAL_LF_HERE< TAB >ACTUAL_TAB_HERE< SLASH >\\< QUOTE >"< HEX >E<'

'' text: '\'CR >\\r< LF >\\n< TAB >\\t< SLASH >\\\\< QUOTE >\\\'< HEX >\\x45<\''
'CR >ACTUAL_CR_HERE< LF >ACTUAL_LF_HERE< TAB >ACTUAL_TAB_HERE< SLASH >\\< QUOTE >\'< HEX >E<'
