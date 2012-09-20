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

		// whitespace-declaration: "@whitespace" _whitespace_ WHITESPACE-MODE
		$whitespace_declaration = new Token(null, array(
			new Literal(null, '@whitespace'),
			new Whitespace(),
			new Alternative('WHITESPACE-MODE', array(
				new Literal(null, 'manual'),
				new Literal(null, 'inline'),
				new Literal(null, 'implicit'),
			))
		));

		// line: [comment | whitespace-declaration | DEFINITION]
		$line = new Multiplier('line', array(
			new Alternative(null, array(
				$comment,
				$whitespace_declaration,
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

	public static function build_root_token($grammar)
	{
		static $hatchet_grammar;
		if(!$hatchet_grammar)
			$hatchet_grammar = new static();

		return static::build($hatchet_grammar->parse($grammar));
	}

	private static $definitions = array();
	/** @var Token[] */
	private static $followup_tokens = array();

	/**
	 * Converts a parsed grammar into a tree of tokens
	 *
	 * A parsed grammar tree is a tree of token definitions.
	 * We need to use those definitions to recursively build
	 * a token tree for the root token.
	 *
	 * Essentially, this:
	 * |-- root
	 * |   |-- a
	 * |   `-- b
	 * |-- a
	 * |   `-- "a"
	 * `-- b
	 *     |-- "b"
	 *     |-- or
	 *     `-- 2
	 * Should become this:
	 * root
	 * `-- "a"
	 * `-- alternative
	 *     |-- "b"
	 *     `-- 2
	 *
	 * @param array $tree
	 * @throws Exception
	 * @return Token
	 */
	private static function build($tree)
	{
		static::$definitions     = array();
		static::$followup_tokens = array();
		$whitespace_mode = null;

		// preparing a list of token definitions
		foreach($tree as $node)
		{
			if($node['name'] == 'WHITESPACE-MODE')
			{
				if(!is_null($whitespace_mode))
					throw new Exception("Parse error: multiple whitespace declarations are not allowed");
				$whitespace_mode = $node['text'];
				continue;
			}

			/*
			 * A node is an array of:
			 * [name] => DEFINITION
			 * [text] => : [line {"\n" line}]
			 * [child_nodes] => array
			 */

			// one token = root def, two = name and body
			$name = (count($node['child_nodes']) > 1) ? $node['child_nodes'][0]['text'] : '';

			if(isset(static::$definitions[$name]))
				throw new Exception("Token $name is already defined");

			static::$definitions[$name] = end($node['child_nodes']);
		}

		// converting token definitions to token trees (without recursive tokens)
		$token = static::get_token(''); // '' is the name of the root token

		// installing recursive tokens
		foreach(static::$followup_tokens as $name=>$t)
		{
			$t->set_definition(array(static::get_token($name)));
		}

		// removing meaningless tokens
		$spt = new Token(null, array($token));
		$spt = static::remove_meaningless_tokens($spt);
		$token = $spt->definition[0];

		// we'll have to come up with something less awful someday
		return array(
			$token,
			$whitespace_mode ?: static::WHITESPACE_INLINE
		);
	}

	private static function remove_meaningless_tokens(Token $token, $visited = array())
	{
		if(in_array($token, $visited))
			return $token;

		$visited[] = $token;

		foreach($token->definition as $k=>$t)
		{
			while(get_class($t) == __NAMESPACE__.'\tokens\Token' && count($t->definition) == 1)
			{
				if(is_null($t->name))
				{
					// removing anonymous tokens that only have one child
					$t = $t->definition[0];
					$token->definition[$k] = $t;
				}
				else if(is_null($t->definition[0]->name) && count($t->definition[0]->definition) == 0)
				{
					// if a named token has only one child that is an anonymous leave, we can remove it too
					// this helps with wrapping _quoted_ in a token to give it a name (if we don't remove the
					// wrapper, it will capture whitespace next to the _quoted_)
					$t->definition[0]->name = $t->name;
					$t = $t->definition[0];
					$token->definition[$k] = $t;
				}
				else
				{
					break;
				}
			}

			$token->definition[$k] = static::remove_meaningless_tokens($token->definition[$k], $visited);
		}

		return $token;
	}

	/**
	 * Returns a token by name, creates it if necessary
	 *
	 * @param string $name
	 * @throws Exception
	 * @return Token
	 */
	private static function get_token($name)
	{
		if(!isset(static::$definitions[$name]))
		{
			if($name == '_quoted_')
				static::$definitions[$name] = new QuotedString();
			else if($name == '_whitespace_')
				static::$definitions[$name] = new Whitespace();
			else
				throw new Exception('Cannot find definition for '.($name == '' ? 'root token' : $name));
		}

		if(static::$definitions[$name] instanceof Token)
			return static::$definitions[$name];

		$body = static::$definitions[$name];
		$token = static::build_token_by_tree($body);
		$token->name = $name;

		static::$definitions[$name] = $token;
		return $token;
	}

	/**
	 * Creates a token by its definition tree
	 *
	 * @param array $node
	 * @throws Exception
	 * @return Token
	 */
	private static function build_token_by_tree($node)
	{
		// This name is not a node name in terms of our new grammar (that we're parsing);
		// it's a name in terms of the grammar grammar (that we parse with).
		// So in terms of building new tokens it's actually a type, not a name.
		$type = $node['name'];
		switch($type)
		{
			case 'REGEXP':
				return new Regexp(null, $node['text']);

			case 'LITERAL':
				return new Literal(null, QuotedString::decode($node['text']));

			case 'BODY':
				return new Token(null, static::build_tokens($node['child_nodes']));

			case 'CONDITION':
				return new Multiplier(null, static::build_tokens($node['child_nodes']), true);

			case 'MULTIPLIER':
				return new Multiplier(null, static::build_tokens($node['child_nodes']));

			case 'NAME':
				// I will take these cotton balls from you with my hand and put them in my pocket.
				// That is, here we replace name of a token with the actual token
				// (which we built from its definiton).
				// Unfortunately, we cannot just return get_token() here because of recursive definitions.
				// We need to return something NOW, and when all definitions are built we can
				// follow up and insert correct references to these temporary tokens.
				$token =& static::$followup_tokens[$node['text']];
				if(!isset($token))
				{
					$token = new Token(); // this actually creates an enormous amount of anonymous tokens
					static::get_token($node['text']); // make sure we build it
				}
				return static::$followup_tokens[$node['text']];

			case 'ALTERNATIVE-TOKENS':
				return new Alternative(null, static::build_tokens($node['child_nodes']));
		}

		throw new Exception('Cannot build grammar token ' . $type);
	}

	/**
	 * @param array $nodes
	 * @return Token[]
	 */
	private static function build_tokens($nodes)
	{
		$tokens = array();
		foreach($nodes as $node)
		{
			$tokens[] = static::build_token_by_tree($node);
		}
		return $tokens;
	}
}
