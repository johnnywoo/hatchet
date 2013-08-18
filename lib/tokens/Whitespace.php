<?php

namespace Hatchet\Tokens;

/**
 * A special token for whitespace
 *
 * Whitespace in Hatchet is implicit, any number of [ \t] can be present
 * between tokens. This means that a regexp or literal cannot start with
 * whitespace. If you need to force a whitespace char between tokens,
 * use _whitespace_:
 *   list: word {_whitespace_ word}
 */
class Whitespace extends Token
{
	private $chars = array(' ', "\t");

	public function scan(&$text, $whitespaceModeRegexp)
    {
        if ($text !== '') {
            $char = substr($text, 0, 1);
            if (in_array($char, $this->chars)) {
                $text = substr($text, 1);
                return array(
                    'name'       => $this->name,
                    'childNodes' => array(),
                    'text'       => $char,
                );
            }
        }
        return null;
    }
}
