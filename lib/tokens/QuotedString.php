<?

namespace hatchet\tokens;

class QuotedString extends Token
{
	public function scan(&$text)
	{
		// implicit whitespace
		$text = preg_replace("/^[ \t]+/", '', $text);

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

	public static function decode($quoted_string)
	{
		return eval('return ' . $quoted_string . ';');
	}
}