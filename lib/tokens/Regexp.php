<?

namespace hatchet\tokens;

class Regexp extends Token
{
	private $regexp = '';

	public function __construct($name, $regexp)
	{
		parent::__construct($name, array());
		$this->regexp = '/^'.substr($regexp, 1);
	}

	public function scan(&$text)
	{
		// implicit whitespace
		$text = preg_replace("/^[ \t]+/", '', $text);

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