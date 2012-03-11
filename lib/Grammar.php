<?

namespace hatchet;
use hatchet\tokens\Token;

class Grammar
{
	/** @var \hatchet\tokens\Token */
	protected $root_token;

	/**
	 * @param string $grammar
	 */
	public function __construct($grammar)
	{
		$this->root_token = HatchetGrammar::build_root_token($grammar);
	}

	public function parse($text)
	{
		$ans = $this->root_token->scan($text);

		if(is_null($ans))
			throw new Exception('Parse error: root token not found');

		// implicit whitespace
		$text = preg_replace("/^[ \t]+/", '', $text);

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
}