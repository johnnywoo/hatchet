<?php

namespace Hatchet\Tokens;

use Hatchet\Exception;

class QuotedString extends Token
{
    public function scan(&$text, $whitespaceModeRegexp)
    {
        // implicit whitespace
        if ($whitespaceModeRegexp) {
            $text = preg_replace($whitespaceModeRegexp, '', $text);
        }

        if (preg_match('/^"(\\\\.|[^\\\\"]+)*"/', $text, $m)) {
            $text = substr($text, strlen($m[0]));
            return array(
                'name'       => $this->name,
                'childNodes' => array(),
                'text'       => $m[0],
            );
        }
        if (preg_match("/^'(\\\\.|[^\\\\']+)*'/", $text, $m)) {
            $text = substr($text, strlen($m[0]));
            return array(
                'name'       => $this->name,
                'childNodes' => array(),
                'text'       => $m[0],
            );
        }
        return null;
    }

    public static function decode($quotedString)
    {
        $quote = substr($quotedString, 0, 1);
        return preg_replace_callback(
            '/\\\\([' . $quote . 'trn\\\\])|\\\\x([0-9A-Fa-f]{2})/',
            function ($m) use ($quote) {
                if (!empty($m[1])) {
                    switch ($m[1]) {
                        case '\\':   return '\\';
                        case $quote: return $quote;
                        case 'n':    return "\n";
                        case 'r':    return "\r";
                        case 't':    return "\t";
                    }
                }

                if (isset($m[2]) && strlen($m[2]) == 2) {
                    return chr(hexdec($m[2]));
                }

                throw new Exception('Really weird escape sequence encountered: ' . $m[0]);
            },
            substr($quotedString, 1, -1) // removing the quotes
        );
    }
}
