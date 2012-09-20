<?

namespace hatchet\tokens;

class QuotedString extends Token
{
	public function scan(&$text, $whitespace_mode_regexp)
	{
		// implicit whitespace
		if($whitespace_mode_regexp)
			$text = preg_replace($whitespace_mode_regexp, '', $text);

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
