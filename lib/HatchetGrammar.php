<?

namespace hatchet;
use hatchet\tokens\Token;
use hatchet\tokens\Alternative;
use hatchet\tokens\Literal;
use hatchet\tokens\Multiplier;
use hatchet\tokens\QuotedString;
use hatchet\tokens\Regexp;

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
		// name: /[a-z0-9_-]+/
		$name = new Regexp('name', '/[a-z0-9_-]+/');
		// regexp: /\/[^\n]*+/
		$regexp = new Regexp('regexp', '/\/[^\n]*+/');
		// whitespace: /[ \t]/
		$whitespace = new Regexp('whitespace', '/[ \t]/');
		// native-name: "_quoted_"
		$native_name = new Literal('native-name', '_quoted_');

		// token: _quoted_ | name | native-name
		$token = new Alternative('token', array(
			new QuotedString(),
			$native_name,
			$name,
		));

		// definition: name ":" (_regexp | token {whitespace token})
		$definition = new Token('definition', array(
			$name,
			new Literal(null, ':'),
			new Alternative(null, array(
				$regexp,
				new Token(null, array(
					$token,
					new Multiplier(null, array(
						$whitespace,
						$token,
					))
				)),
			))
		));

		// line: [comment | definition] "\n"
		$line = new Token('line', array(
			new Multiplier(null, array($comment, $definition), false),
			new Literal(null, "\n")
		));

		// : {line}
		$root = new Multiplier('', array($line));

		$this->root_token = $root;
	}
}