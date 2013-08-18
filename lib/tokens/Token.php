<?php

namespace hatchet\tokens;

class Token
{
	public $name = null;

	/**
	 * Definition of the token
	 * @var Token[]
	 */
	public $definition = array();

	/**
	 * @param null|string $name
	 * @param array $definition
	 */
	public function __construct($name = null, array $definition = array())
	{
		$this->name = $name;
		$this->setDefinition($definition);
	}

	public function setDefinition(array $tokens)
	{
		$this->definition = $tokens;
	}

	/**
	 * Reads the text from start, cuts everything that matches the token's definition
	 *
	 * Return NULL if the token is not found; a data array otherwise.
	 *
	 * @param string $text
	 * @param string $whitespaceModeRegexp
	 * @return array|null
	 */
    public function scan(&$text, $whitespaceModeRegexp)
    {
        $origText = $text;

        $childNodes = array();
        foreach ($this->definition as $token) {
            $node = $token->scan($text, $whitespaceModeRegexp);
            if (is_null($node)) {
                return null;
            }

            $childNodes[] = $node;
        }

        return array(
            'name'       => $this->name,
            'childNodes' => $childNodes,
            'text'       => static::findShiftedText($origText, $text),
        );
    }

    public static function findShiftedText($origText, $newText)
    {
        // substr will return false for an empty substring
        if ($origText === $newText) {
            return '';
        }
        return substr($origText, 0, strlen($origText) - strlen($newText));
    }
}
