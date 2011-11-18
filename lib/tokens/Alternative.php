<?

namespace hatchet\tokens;

class Alternative extends Token
{
	public function scan(&$text)
	{
		$orig_text = $text;

		// token
		foreach($this->definition as $token)
		{
			$child = $token->scan($text);
			if(!is_null($child))
			{
				return array(
					'name'        => $this->name,
					'child_nodes' => array($child),
					'text'        => static::find_shifted_text($orig_text, $text),
				);
			}

			$text = $orig_text;
		}

		return null;
	}
}