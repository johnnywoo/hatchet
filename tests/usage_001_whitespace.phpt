--TEST--
Usage: whitespace modes
--FILE--
<?php

require_once '_common.php';

use hatchet\Grammar;
use hatchet\Exception;

$grammar_text = ': "a" "b"';

$parsers = array(
	'manual'   => new Grammar("@whitespace manual\n{$grammar_text}"),
	'inline'   => new Grammar("@whitespace inline\n{$grammar_text}"),
	'implicit' => new Grammar("@whitespace implicit\n{$grammar_text}"),
);

function testWsModes($text)
{
    /** @var $parsers Grammar[] */
    global $parsers;

    echo '== ' . var_export($text, true) . "\n";
    foreach ($parsers as $name => $parser) {
        echo $name . ' ';
        try {
            $parser->parse($text);
            echo "ok\n";
        } catch (hatchet\Exception $e) {
            echo "parse error\n";
        }
    }
}

testWsModes('ab');
testWsModes(' a	b '); // spaces and tabs
testWsModes('
a
b
');

?>
--EXPECT--
== 'ab'
manual ok
inline ok
implicit ok
== ' a	b '
manual parse error
inline ok
implicit ok
== '
a
b
'
manual parse error
inline parse error
implicit ok
