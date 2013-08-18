--TEST--
Calc grammar: addition
--FILE--
<?php

require_once '_common.php';

testCalc('1');
testCalc('2 + 2');
testCalc('-1 + -2');
testCalc('-10 + 5 - 1 + 100');

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
