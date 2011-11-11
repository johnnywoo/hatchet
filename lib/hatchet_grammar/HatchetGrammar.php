<?

namespace hatchet\hatchet_grammar;
use hatchet\Grammar;
use hatchet\Token;
use hatchet\Token_QuotedString;

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
		$comment = new Regexp('/#[^\n]*+/');
		// name: /[a-z0-9_-]+/
		$name = new Regexp('/[a-z0-9_-]+/');
		// regexp: /\/[^\n]*+/
		$regexp = new Regexp('/\/[^\n]*+/');
		// whitespace: /[ \t]/
		$whitespace = new Regexp('/[ \t]/');
		// native-name: "_quoted_"
		$native_name = new Alternative(array(
			new Literal('_quoted_'),
			new Literal(''),
		));

		// token: _quoted_ | name | native-name
		$token = new Alternative(array(
			new Token_QuotedString(),
			$native_name,
			$name,
		));

		// definition: name ":" (_regexp | token {whitespace token})
		$definition = new Token(array(
			$name,
			new Literal(':'),
			new Alternative(array(
				$regexp,
				new Token(array(
					$token,
					new Multiplier(array(
						$whitespace,
						$token,
					))
				)),
			))
		));

		// line: [comment | definition] "\n"
		$line = new Token(array(
			new Multiplier(array($comment, $definition), false),
			new Literal("\n")
		));

		// : {line}
		$root = new Multiplier(array($line));

		$this->root_token = $root;
	}
}