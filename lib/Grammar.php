<?

namespace hatchet;
use hatchet\tokens\Token;

class Grammar
{
	const WHITESPACE_MANUAL   = 'manual';
	const WHITESPACE_INLINE   = 'inline';
	const WHITESPACE_IMPLICIT = 'implicit';

	protected $whitespace_mode = self::WHITESPACE_INLINE;

	/** @var \hatchet\tokens\Token */
	protected $root_token;

	/**
	 * @param string $grammar
	 */
	public function __construct($grammar)
	{
		list($this->root_token, $this->whitespace_mode) = HatchetGrammar::build_root_token($grammar);
	}

	public function parse($text)
	{
		$ws_regexp = $this->get_ws_mode_regexp($this->whitespace_mode);

		$ans = $this->root_token->scan($text, $ws_regexp);

		if(is_null($ans))
			throw new Exception('Parse error: root token not found');

		// implicit whitespace
		if($ws_regexp)
			$text = preg_replace($ws_regexp, '', $text);

		if(strlen($text))
			throw new Exception('Parse error: root token does not cover the whole text');

		return $this->makeup_tree(array($ans));
	}

	protected function create_nodes($name, $text, array $child_nodes)
	{
		return array(array(
			'name'        => $name,
			'text'        => $text,
			'child_nodes' => $child_nodes,
		));
	}

	private function makeup_tree($nodes)
	{
		$new_nodes = array();
		foreach($nodes as $node)
		{
			$child_nodes = $this->makeup_tree($node['child_nodes']);

			// internal anonymous nodes should not make it to the callback
			if(is_null($node['name']))
				$append = $child_nodes;
			else
				$append = $this->create_nodes($node['name'], $node['text'], $child_nodes);

			$new_nodes = array_merge($new_nodes, $append);
		}
		return $new_nodes;
	}

	private function get_ws_mode_regexp($ws_mode)
	{
		switch($ws_mode)
		{
			case static::WHITESPACE_MANUAL:   return '';
			case static::WHITESPACE_INLINE:   return "/^[ \t]+/";
			case static::WHITESPACE_IMPLICIT: return "/^\s+/";
		}
		throw new Exception("Unknown whitespace mode: {$ws_mode}");
	}
}
