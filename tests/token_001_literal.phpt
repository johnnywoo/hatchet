--TEST--
Basic test: literal
--ARGS--
--FILE--
<?php

require_once '_common.php';

use Hatchet\Grammar;
use Hatchet\Tokens\Literal;

class TestGrammar1 extends Grammar
{
    public function __construct()
    {
        $this->rootToken = new Literal('', 'a');
    }
}

$grammar = new TestGrammar1();
dumpTree($grammar->parse('a'));

?>
--EXPECT--
'' text: 'a'
