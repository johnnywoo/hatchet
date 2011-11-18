<?

namespace hatchet;

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
		$this->definition = $definition;
	}

	/**
	 * Reads the text from start, cuts everything that matches the token's definition
	 *
	 * Return NULL if the token is not found; a data array otherwise.
	 *
	 * @param string $text
	 * @return array|null
	 */
	public function scan(&$text)
	{
		$orig_text = $text;

		$subtrees = array();
		foreach($this->definition as $token)
		{
			$subtree = $token->scan($text);
			if(is_null($subtree))
				return null;

			$subtrees[] = $subtree;
		}

		return array(
			'name'        => $this->name,
			'child_nodes' => $subtrees,
			'text'        => static::find_shifted_text($orig_text, $text),
		);
	}

	public function find_shifted_text($orig_text, $new_text)
	{
		// substr will return false for an empty substring
		if($orig_text === $new_text)
			return '';
		return substr($orig_text, 0, strlen($orig_text) - strlen($new_text));
	}
}