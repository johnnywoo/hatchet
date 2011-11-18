<?

namespace hatchet\hatchet_grammar;
use hatchet\Token;

class Literal extends Token
{
	private $literal = '';

	public function __construct($name, $literal)
	{
		parent::__construct($name, array());
		$this->literal = $literal;
	}

	public function scan(&$text)
	{
		$length = strlen($this->literal);
		if(substr($text, 0, $length) == $this->literal)
		{
			$text = ($text == $this->literal) ? '' : substr($text, $length);
			return array(
				'name'        => $this->name,
				'child_nodes' => array(),
				'text'        => $this->literal,
			);
		}
		return null;
	}
}