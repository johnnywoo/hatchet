--TEST--
Calc grammar: multiplication
--FILE--
<?php

require_once '_common.php';

test_calc('1 * 2');
test_calc('1 * 10 / 2');
test_calc('-5 * 10 / 5 * 2');
test_calc('10 / -5 / 2');

?>
--EXPECT--
Expression: 1 * 2
Result: 2

Expression: 1 * 10 / 2
Result: 5

Expression: -5 * 10 / 5 * 2
Result: -20

Expression: 10 / -5 / 2
Result: -1