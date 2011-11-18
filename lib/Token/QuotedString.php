<?

namespace hatchet;

class Token_QuotedString extends Token
{
	public function scan(&$text)
	{
		if(preg_match('/^".*?"/', $text, $m))
		{
			$text = substr($text, strlen($m[0]));
			return array(
				'name'        => $this->name,
				'child_nodes' => array(),
				'text'        => $m[0],
			);
		}
		return null;
	}
}