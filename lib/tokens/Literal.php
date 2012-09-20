<?

namespace hatchet\tokens;

class Literal extends Token
{
	private $literal = '';

	public function __construct($name, $literal)
	{
		parent::__construct($name);
		$this->literal = $literal;
	}

	public function scan(&$text, $whitespace_mode_regexp)
	{
		// implicit whitespace
		if($whitespace_mode_regexp)
			$text = preg_replace($whitespace_mode_regexp, '', $text);

		$length = strlen($this->literal);
		if(substr($text, 0, $length) == $this->literal)
		{
			$text = ($text === $this->literal) ? '' : substr($text, $length);
			return array(
				'name'        => $this->name,
				'child_nodes' => array(),
				'text'        => $this->literal,
			);
		}
		return null;
	}
}
