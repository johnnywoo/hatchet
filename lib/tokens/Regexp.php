<?php

namespace hatchet\tokens;

class Regexp extends Token
{
	private $regexp = '';

	public function __construct($name, $regexp)
	{
		parent::__construct($name);
		$this->regexp = '/^'.substr($regexp, 1);
	}

	public function scan(&$text, $whitespaceModeRegexp)
    {
        // implicit whitespace
        if ($whitespaceModeRegexp) {
            $text = preg_replace($whitespaceModeRegexp, '', $text);
        }

        if (preg_match($this->regexp, $text, $m)) {
            $text = substr($text, strlen($m[0]));
            return array(
                'name'       => $this->name,
                'childNodes' => array(),
                'text'       => $m[0],
            );
        }
        return null;
    }
}
