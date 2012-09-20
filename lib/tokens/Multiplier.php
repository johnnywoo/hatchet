<?

namespace hatchet\tokens;

class Multiplier extends Token
{
	private $only_one_or_zero = false;

	/**
	 * @param string $name
	 * @param Token[] $definition
	 * @param bool $only_one_or_zero  FALSE = [], TRUE = {}
	 */
	public function __construct($name, array $definition, $only_one_or_zero = false)
	{
		parent::__construct($name, $definition);
		$this->only_one_or_zero = $only_one_or_zero;
	}

	public function scan(&$text, $whitespace_mode_regexp)
	{
		$orig_text = $text;

		$child_nodes = array();

		do
		{
			$one_pass_orig_text = $text;
			$node = parent::scan($text, $whitespace_mode_regexp);
			if(is_null($node))
			{
				// backtrack
				$text = $one_pass_orig_text;
				break;
			}

			// match found, but it has no text
			if($text === $one_pass_orig_text)
				break;

			// the pass succeeded: add found nodes to the list
			$child_nodes = array_merge($child_nodes, $node['child_nodes']);
		}
		while(!$this->only_one_or_zero);

		return array(
			'name'        => count($child_nodes) ? $this->name : null,
			'child_nodes' => $child_nodes,
			'text'        => static::find_shifted_text($orig_text, $text),
		);
	}
}
