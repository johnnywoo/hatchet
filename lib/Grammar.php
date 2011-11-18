<?

namespace hatchet;

class Grammar
{
	/** @var \hatchet\tokens\Token */
	protected $root_token;

	/**
	 * @param string $grammar
	 */
	public function __construct($grammar)
	{
		$hatchet_grammar = new HatchetGrammar();
		$this->root_token = $hatchet_grammar->parse($grammar);
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

		$tree = $this->makeup_tree(array($ans));
		return reset($tree);
	}

	private function makeup_tree($nodes)
	{
		$new_nodes = array();
		foreach($nodes as $node)
		{
			$node['child_nodes'] = $this->makeup_tree($node['child_nodes']);

			if(is_null($node['name']))
			{
				$new_nodes = array_merge($new_nodes, $node['child_nodes']);
			}
			else
			{
				$new_nodes[] = $node;
			}
		}
		return $new_nodes;
	}
}