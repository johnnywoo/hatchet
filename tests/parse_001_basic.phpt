--TEST--
Basic test: whole grammar is one simple quoted string
--ARGS--
--SKIPIF--
Skipped: not implemented yet.
--FILE--
<?php

require_once '_common.php';
use hatchet\Grammar;

$grammar = new Grammar(':"a"');
dump_tree($grammar->parse('a'));

?>
--EXPECT--
name: '' text: 'a'