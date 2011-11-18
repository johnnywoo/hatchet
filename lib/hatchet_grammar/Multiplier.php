<?

namespace hatchet\hatchet_grammar;
use hatchet\Token;

class Multiplier extends Token
{
	private $only_one_or_zero = false;

	/**
	 * @param string $name
	 * @param \hatchet\Token[] $definition
	 * @param bool $only_one_or_zero  FALSE = [], TRUE = {}
	 */
	public function __construct($name, array $definition, $only_one_or_zero = false)
	{
		parent::__construct($name, $definition);
		$this->only_one_or_zero = $only_one_or_zero;
	}

	public function scan(&$text)
	{
		$orig_text = $text;

		$child_nodes = array();

		do
		{
			$one_pass_child_nodes = array();
			$one_pass_orig_text = $text;
			// token
			foreach($this->definition as $token)
			{
				$node = $token->scan($text);
				if(is_null($node))
				{
					$text = $one_pass_orig_text;
					break 2; // stop the whole search
				}

				$one_pass_child_nodes[] = $node;
			}

			// the pass succeeded: add found nodes to the list
			$child_nodes = array_merge($child_nodes, $one_pass_child_nodes);
		}
		while(!$this->only_one_or_zero);

		return array(
			'name'        => $this->name,
			'child_nodes' => $child_nodes,
			'text'        => static::find_shifted_text($orig_text, $text),
		);
	}
}