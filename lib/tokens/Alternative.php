<?php

namespace hatchet\tokens;

class Alternative extends Token
{
    public function scan(&$text, $whitespaceModeRegexp)
    {
        $origText = $text;

        // token
        foreach ($this->definition as $token) {
            $child = $token->scan($text, $whitespaceModeRegexp);
            if (!is_null($child)) {
                return array(
                    'name'       => $this->name,
                    'childNodes' => array($child),
                    'text'       => static::findShiftedText($origText, $text),
                );
            }

            $text = $origText;
        }

        return null;
    }
}
