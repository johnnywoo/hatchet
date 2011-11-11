<?

namespace hatchet\hatchet_grammar;
use hatchet\Token;

class Multiplier extends Token
{
	private $only_one_or_zero = false;

	/**
	 * @param array $definition
	 * @param bool $only_one_or_zero  FALSE = [], TRUE = {}
	 */
	public function __construct(array $definition, $only_one_or_zero = false)
	{
		$this->definition = $definition;
		$this->only_one_or_zero = $only_one_or_zero;
	}

	public function scan(&$text)
	{
		$other = clone $this;
		$orig_text = $text;

		// token
		foreach($this->definition as $token)
		{
			/** @var $data_token \hatchet\Token */
			$data_token = clone $token;
			if(!$data_token->scan($text))
			{
				$text = $orig_text;
				return true;
			}
		}

		if(!$this->only_one_or_zero && $other->scan($text))
			$this->children = array_merge($this->children, $other->children);

		return true;
	}
}