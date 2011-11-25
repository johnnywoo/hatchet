<?

namespace hatchet;
use hatchet\tokens\Token;
use hatchet\tokens\Alternative;
use hatchet\tokens\Literal;
use hatchet\tokens\Multiplier;
use hatchet\tokens\QuotedString;
use hatchet\tokens\Regexp;
use hatchet\tokens\Whitespace;

/**
 * A standard grammar for hatchet grammar file
 *
 * We use this predefined grammar to parse user-provided grammar files
 * with Hatchet itself.
 */
class HatchetGrammar extends Grammar
{
	public function __construct()
	{
		// comment: /#[^\n]*+/
		$comment = new Regexp('comment', '/#[^\n]*+/');
		// NAME: /[a-z0-9_-]+/
		$name = new Regexp('NAME', '/[a-zA-Z0-9_-]+/');
		// REGEXP: /\/[^\n]*+/
		$regexp = new Regexp('REGEXP', '/\/[^\n]*+/');

		// NATIVE-NAME: "_quoted_" | "_whitespace_"
		$native_name = new Alternative('NATIVE-NAME', array(
			new Literal(null, '_quoted_'),
			new Literal(null, '_whitespace_'),
		));

		// grouping: "(" ALTERNATIVE-TOKENS ")"
		$grouping   = new Token('grouping');
		// CONDITION: "[" ALTERNATIVE-TOKENS "]"
		$condition  = new Token('CONDITION');
		// MULTIPLIER: "{" ALTERNATIVE-TOKENS "}"
		$multiplier = new Token('MULTIPLIER');

		// token: LITERAL | NAME | NATIVE-NAME | grouping | CONDITION | MULTIPLIER
		$token = new Alternative('token', array(
			new QuotedString('LITERAL'),
			$name,
			$native_name,
			$grouping,
			$condition,
			$multiplier,
		));

		// TOKENS: token {_whitespace_ token}
		$tokens = new Token('TOKENS', array(
			$token,
			new Multiplier(null, array(
				new Whitespace(),
				$token,
			)),
		));

		// ALTERNATIVE-TOKENS: TOKENS {"|" TOKENS}
		$alt = new Token('ALTERNATIVE-TOKENS', array(
			$tokens,
			new Multiplier(null, array(
				new Literal(null, "|"),
				$tokens,
			)),
		));

		// filling in recursive definitions
		$grouping->set_definition(array(new Literal(null, '('), $alt, new Literal(null, ')')));
		$condition->set_definition(array(new Literal(null, '['), $alt, new Literal(null, ']')));
		$multiplier->set_definition(array(new Literal(null, '{'), $alt, new Literal(null, '}')));

		// BODY: ALTERNATIVE-TOKENS
		$body = new Token('BODY', array($alt));

		// DEFINITION: [NAME] ":" (REGEXP | BODY)
		$definition = new Token('DEFINITION', array(
			new Multiplier(null, array($name), true),
			new Literal(null, ':'),
			new Alternative(null, array($regexp, $body)),
		));

		// line: [comment | DEFINITION]
		$line = new Multiplier('line', array(
			new Alternative(null, array(
				$comment,
				$definition,
			)),
		), false);

		// : [line {"\n" line}]
		$root = new Multiplier('', array(
			$line,
			new Multiplier(null, array(
				new Literal(null, "\n"),
				$line,
			)),
		), true); // only one or zero

		$this->root_token = $root;
	}

	protected function create_nodes($name, $text, array $child_nodes)
	{
		// convention: names not starting with an uppercase letter are anonymous (including root)
		if(empty($name))
			return $child_nodes;
		$letter = substr($name, 0, 1);
		if(strtolower($letter) == $letter)
			return $child_nodes;

		// a bit of optimization

		if($name == 'TOKENS' && count($child_nodes) <= 1)
			return $child_nodes;

		if($name == 'ALTERNATIVE-TOKENS' && count($child_nodes) <= 1)
		{
			if(empty($child_nodes))
				return array();
			$child = reset($child_nodes);
			if($child['name'] == 'TOKENS')
				return $child['child_nodes'];
			return $child_nodes;
		}

		return parent::create_nodes($name, $text, $child_nodes);
	}
}