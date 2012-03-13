--TEST--
Calc grammar: negative subtraction
--FILE--
<?php

require_once '_common.php';

test_calc('0-2');
test_calc('0--2');
test_calc('0 - -2');
test_calc('0 - - 2'); // error

?>
--EXPECT--
Expression: 0-2
Result: -2

Expression: 0--2
Result: 2

Expression: 0 - -2
Result: 2

Expression: 0 - - 2
Exception: Parse error: root token does not cover the whole text