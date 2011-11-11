<?

namespace hatchet\hatchet_grammar;
use hatchet\Token;

class Regexp extends Token
{
	private $regexp = '';

	public function __construct($regexp)
	{
		$this->regexp = '/^'.substr($regexp, 1);
	}

	public function scan(&$text)
	{
		if(preg_match($this->regexp, $text, $m))
		{
			$this->text = $m[0];
			$text = substr($text, strlen($this->text));
			return true;
		}

		return false;
	}
}