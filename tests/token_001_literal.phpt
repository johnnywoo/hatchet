--TEST--
Basic test: literal
--ARGS--
--FILE--
<?php

require_once '_common.php';

use hatchet\Grammar;
use hatchet\tokens\Literal;

class TestGrammar1 extends Grammar
{
    public function __construct()
    {
        $this->root_token = new Literal('', 'a');
    }
}

$grammar = new TestGrammar1();
dumpTree($grammar->parse('a'));

?>
--EXPECT--
'' text: 'a'
