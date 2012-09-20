<?

namespace hatchet\tokens;

class Regexp extends Token
{
	private $regexp = '';

	public function __construct($name, $regexp)
	{
		parent::__construct($name);
		$this->regexp = '/^'.substr($regexp, 1);
	}

	public function scan(&$text, $whitespace_mode_regexp)
	{
		// implicit whitespace
		if($whitespace_mode_regexp)
			$text = preg_replace($whitespace_mode_regexp, '', $text);

		if(preg_match($this->regexp, $text, $m))
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
