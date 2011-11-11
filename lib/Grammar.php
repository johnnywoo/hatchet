<?

namespace hatchet;
use hatchet\hatchet_grammar\HatchetGrammar;

class Grammar
{
	/** @var Token */
	protected $root_token;

	/**
	 * @param string $grammar
	 */
	public function __construct($grammar)
	{
		$hatchet_grammar = new HatchetGrammar();
		$this->root_token = $hatchet_grammar->parse($grammar);
	}

	public function parse($text)
	{
		/** @var $token Token */
		$token = clone $this->root_token;
		if(!$token->scan($text))
			throw new \Exception('Parse error: root token not found');
		if(strlen($text))
			throw new \Exception('Parse error: root token does not cover the whole text');
		return $token;
	}
}