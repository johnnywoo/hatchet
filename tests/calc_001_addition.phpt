--TEST--
Calc grammar: addition
--FILE--
<?php

require_once '_common.php';

test_calc('1');
test_calc('2 + 2');
test_calc('-1 + -2');
test_calc('-10 + 5 - 1 + 100');

?>
--EXPECT--
Expression: 1
Result: 1

Expression: 2 + 2
Result: 4

Expression: -1 + -2
Result: -3

Expression: -10 + 5 - 1 + 100
Result: 94