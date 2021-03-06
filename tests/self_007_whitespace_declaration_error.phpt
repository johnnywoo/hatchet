--TEST--
Our own grammar: multiple whitespace declarations are not allowed
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Exception;

try {
    new Grammar('
		@whitespace inline
		@whitespace inline
		: "a"
	');
    echo "this should not happen";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}

?>
--EXPECT--
Hatchet\Exception: Parse error: multiple whitespace declarations are not allowed
