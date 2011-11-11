<?

namespace hatchet\hatchet_grammar;
use hatchet\Token;

class Literal extends Token
{
	private $literal = '';

	public function __construct($literal)
	{
		$this->literal = $literal;
	}

	public function scan(&$text)
	{
		$length = strlen($this->literal);
		if(substr($text, 0, $length) == $this->literal)
		{
			$text = ($text == $this->literal) ? '' : substr($text, $length);
			$this->text = $this->literal;
			return true;
		}
		return false;
	}
}