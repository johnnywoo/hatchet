<?php

namespace Hatchet\Tokens;

class Literal extends Token
{
    private $literal = '';

    public function __construct($name, $literal)
    {
        parent::__construct($name);
        $this->literal = $literal;
    }

    public function scan(&$text, $whitespaceModeRegexp)
    {
        // implicit whitespace
        if ($whitespaceModeRegexp) {
            $text = preg_replace($whitespaceModeRegexp, '', $text);
        }

        $length = strlen($this->literal);
        if (substr($text, 0, $length) == $this->literal) {
            $text = ($text === $this->literal) ? '' : substr($text, $length);
            return array(
                'name'       => $this->name,
                'childNodes' => array(),
                'text'       => $this->literal,
            );
        }
        return null;
    }
}
