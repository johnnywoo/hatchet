<?

namespace hatchet;

class Token_QuotedString extends Token
{
	public function scan(&$text)
	{
		if(preg_match('/^".*?"/', $text, $m))
		{
			$this->text = $m[0];
			$text = substr($text, strlen($this->text));
			return true;
		}

		return false;
	}
}