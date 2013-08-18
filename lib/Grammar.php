<?php

namespace Hatchet;

use Hatchet\Tokens\Token;

class Grammar
{
	const WHITESPACE_MANUAL   = 'manual';
	const WHITESPACE_INLINE   = 'inline';
	const WHITESPACE_IMPLICIT = 'implicit';

	protected $whitespaceMode = self::WHITESPACE_INLINE;

	/** @var Token */
	protected $rootToken;

	/**
	 * @param string $grammar
	 */
	public function __construct($grammar)
	{
		list($this->rootToken, $this->whitespaceMode) = HatchetGrammar::buildRootToken($grammar);
	}

	public function parse($text)
	{
		$whitespaceRegexp = $this->getWhitespaceModeRegexp($this->whitespaceMode);

		$ans = $this->rootToken->scan($text, $whitespaceRegexp);

        if (is_null($ans)) {
            throw new Exception('Parse error: root token not found');
        }

        // implicit whitespace
        if ($whitespaceRegexp) {
            $text = preg_replace($whitespaceRegexp, '', $text);
        }

        if (strlen($text)) {
            throw new Exception('Parse error: root token does not cover the whole text');
        }

        return $this->makeupTree(array($ans));
	}

	protected function createNodes($name, $text, array $childNodes)
	{
		return array(array(
			'name'       => $name,
			'text'       => $text,
			'childNodes' => $childNodes,
		));
	}

	private function makeupTree($nodes)
    {
        $newNodes = array();
        foreach ($nodes as $node) {
            $childNodes = $this->makeupTree($node['childNodes']);

            // internal anonymous nodes should not make it to the callback
            if (is_null($node['name'])) {
                $append = $childNodes;
            } else {
                $append = $this->createNodes($node['name'], $node['text'], $childNodes);
            }

            $newNodes = array_merge($newNodes, $append);
        }
        return $newNodes;
    }

    private function getWhitespaceModeRegexp($whitespaceMode)
    {
        switch ($whitespaceMode) {
            case static::WHITESPACE_MANUAL:
                return '';
            case static::WHITESPACE_INLINE:
                return '/^[ \t]+/';
            case static::WHITESPACE_IMPLICIT:
                return '/^\s+/';
        }
        throw new Exception("Unknown whitespace mode: {$whitespaceMode}");
    }
}
