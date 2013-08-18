<?php

namespace Hatchet\Tokens;

class Multiplier extends Token
{
	private $onlyOneOrZero = false;

	/**
	 * @param string $name
	 * @param Token[] $definition
	 * @param bool $onlyOneOrZero  FALSE = [], TRUE = {}
	 */
	public function __construct($name, array $definition, $onlyOneOrZero = false)
	{
		parent::__construct($name, $definition);
		$this->onlyOneOrZero = $onlyOneOrZero;
	}

    public function scan(&$text, $whitespaceModeRegexp)
    {
        $origText = $text;
        $childNodes = array();

        do {
            $onePassOrigText = $text;
            $node = parent::scan($text, $whitespaceModeRegexp);
            if (is_null($node)) {
                // backtrack
                $text = $onePassOrigText;
                break;
            }

            // match found, but it has no text
            if ($text === $onePassOrigText) {
                break;
            }

            // the pass succeeded: add found nodes to the list
            $childNodes = array_merge($childNodes, $node['childNodes']);
        } while (!$this->onlyOneOrZero);

        return array(
            'name'       => count($childNodes) ? $this->name : null,
            'childNodes' => $childNodes,
            'text'       => static::findShiftedText($origText, $text),
        );
    }
}
