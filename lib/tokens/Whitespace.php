<?

namespace hatchet\tokens;

class Whitespace extends Token
{
	private $chars = array(' ', "\t");

	public function scan(&$text)
	{
		if($text !== '')
		{
			$char = substr($text, 0, 1);
			if(in_array($char, $this->chars))
			{
				$text = substr($text, 1);
				return array(
					'name'        => $this->name,
					'child_nodes' => array(),
					'text'        => $char,
				);
			}
		}
		return null;
	}
}