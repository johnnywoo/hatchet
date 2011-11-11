<?

namespace hatchet\hatchet_grammar;
use hatchet\Token;

class Alternative extends Token
{
	public function scan(&$text)
	{
		$orig_text = $text;

		// token
		foreach($this->definition as $token)
		{
			/** @var $data_token \hatchet\Token */
			$data_token = clone $token;
			if($data_token->scan($text))
				return true;

			$text = $orig_text;
		}

		return false;
	}
}