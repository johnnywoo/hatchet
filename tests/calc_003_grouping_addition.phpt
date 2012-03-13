--TEST--
Calc grammar: grouping addition
--FILE--
<?php

require_once '_common.php';

test_calc('0 + (1 + 1)');
test_calc('0 - (1 + 1)');
test_calc('0 - (1 - 1)');
test_calc('0 - (10 - (1 - 6) + 100) + 1000');

?>
--EXPECT--
Expression: 0 + (1 + 1)
Result: 2

Expression: 0 - (1 + 1)
Result: -2

Expression: 0 - (1 - 1)
Result: 0

Expression: 0 - (10 - (1 - 6) + 100) + 1000
Result: 885