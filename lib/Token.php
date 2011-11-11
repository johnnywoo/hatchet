<?

namespace hatchet;

class Token
{
	public $name = '';

	public $text = '';

	/**
	 * Definition of the token
	 * @var hatchet\Token[]
	 */
	public $definition = array();

	/**
	 * Scanned subtokens
	 * @var hatchet\Token[]
	 */
	public $children = array();

	public function __construct(array $definition = array())
	{
		$this->definition = $definition;
	}

	/**
	 * Reads the text from start, cuts everything that matches the token's definition
	 *
	 * The scan should fill the current object with data (->text and ->children).
	 * If the token if not found, FALSE should be returned; TRUE otherwise.
	 *
	 * @param $text
	 * @return bool success status
	 */
	public function scan(&$text)
	{
		$orig_text = $text;

		foreach($this->definition as $token)
		{
			/** @var $data_token \hatchet\Token */
			$data_token = clone $token;
			if(!$data_token->scan($text))
				return false;

			$this->children[] = $data_token;
		}

		$this->text = substr($orig_text, 0, strlen($orig_text) - strlen($text));

		return true;
	}
}