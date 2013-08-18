<?php

namespace Hatchet;

use Hatchet\Tokens\Token;
use Hatchet\Tokens\Alternative;
use Hatchet\Tokens\Literal;
use Hatchet\Tokens\Multiplier;
use Hatchet\Tokens\QuotedString;
use Hatchet\Tokens\Regexp;
use Hatchet\Tokens\Whitespace;

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
		$grouping->setDefinition(array(new Literal(null, '('), $alt, new Literal(null, ')')));
		$condition->setDefinition(array(new Literal(null, '['), $alt, new Literal(null, ']')));
		$multiplier->setDefinition(array(new Literal(null, '{'), $alt, new Literal(null, '}')));

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

		$this->rootToken = $root;
	}

	protected function createNodes($name, $text, array $childNodes)
    {
        // convention: names not starting with an uppercase letter are anonymous (including root)
        if (empty($name)) {
            return $childNodes;
        }
        $letter = substr($name, 0, 1);
        if (strtolower($letter) == $letter) {
            return $childNodes;
        }

        // a bit of optimization
        if ($name == 'TOKENS' && count($childNodes) <= 1) {
            return $childNodes;
        }

        if ($name == 'ALTERNATIVE-TOKENS' && count($childNodes) <= 1) {
            if (empty($childNodes)) {
                return array();
            }
            $child = reset($childNodes);
            if ($child['name'] == 'TOKENS') {
                return $child['childNodes'];
            }
            return $childNodes;
        }

        return parent::createNodes($name, $text, $childNodes);
    }

    public static function buildRootToken($grammar)
    {
        /** @var HatchetGrammar $hatchetGrammar */
        static $hatchetGrammar;
        if (!$hatchetGrammar) {
            $hatchetGrammar = new static();
        }

        return static::build($hatchetGrammar->parse($grammar));
    }

    private static $definitions = array();
	/** @var Token[] */
	private static $followupTokens = array();

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
		static::$definitions    = array();
		static::$followupTokens = array();

		$whitespace_mode = null;

		// preparing a list of token definitions
        foreach ($tree as $node) {
            if ($node['name'] == 'WHITESPACE-MODE') {
                if (!is_null($whitespace_mode)) {
                    throw new Exception("Parse error: multiple whitespace declarations are not allowed");
                }
                $whitespace_mode = $node['text'];
                continue;
            }

            /*
             * A node is an array of:
             * [name] => DEFINITION
             * [text] => : [line {"\n" line}]
             * [childNodes] => array
             */

            // one token = root def, two = name and body
            $name = (count($node['childNodes']) > 1) ? $node['childNodes'][0]['text'] : '';

            if (isset(static::$definitions[$name])) {
                throw new Exception("Token $name is already defined");
            }

            static::$definitions[$name] = end($node['childNodes']);
        }

        // converting token definitions to token trees (without recursive tokens)
		$token = static::getToken(''); // '' is the name of the root token

		// installing recursive tokens
        foreach (static::$followupTokens as $name => $t) {
            $t->setDefinition(array(static::getToken($name)));
        }

        // removing meaningless tokens
        $spt   = new Token(null, array($token));
        $spt   = static::removeMeaninglessTokens($spt);
        $token = $spt->definition[0];

        // we'll have to come up with something less awful someday
        return array(
            $token,
            $whitespace_mode ? : static::WHITESPACE_INLINE
        );
    }

	private static function removeMeaninglessTokens(Token $token, $visited = array())
    {
        if (in_array($token, $visited)) {
            return $token;
        }

        $visited[] = $token;

        foreach ($token->definition as $k => $t) {
            while (get_class($t) == __NAMESPACE__ . '\tokens\Token' && count($t->definition) == 1) {
                if (is_null($t->name)) {
                    // removing anonymous tokens that only have one child
                    $t = $t->definition[0];
                    $token->definition[$k] = $t;
                } else if (is_null($t->definition[0]->name) && count($t->definition[0]->definition) == 0) {
                    // if a named token has only one child that is an anonymous leave, we can remove it too
                    // this helps with wrapping _quoted_ in a token to give it a name (if we don't remove the
                    // wrapper, it will capture whitespace next to the _quoted_)
                    $t->definition[0]->name = $t->name;
                    $t = $t->definition[0];
                    $token->definition[$k] = $t;
                } else {
                    break;
                }
            }

            $token->definition[$k] = static::removeMeaninglessTokens($token->definition[$k], $visited);
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
	private static function getToken($name)
    {
        if (!isset(static::$definitions[$name])) {
            if ($name == '_quoted_') {
                static::$definitions[$name] = new QuotedString();
            } else if ($name == '_whitespace_') {
                static::$definitions[$name] = new Whitespace();
            } else {
                throw new Exception('Cannot find definition for ' . ($name == '' ? 'root token' : $name));
            }
        }

        if (static::$definitions[$name] instanceof Token) {
            return static::$definitions[$name];
        }

        $body = static::$definitions[$name];

        $token = static::buildTokenByTree($body);
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
	private static function buildTokenByTree($node)
	{
		// This name is not a node name in terms of our new grammar (that we're parsing);
		// it's a name in terms of the grammar grammar (that we parse with).
		// So in terms of building new tokens it's more like a type, not a name.
		$type = $node['name'];
		switch($type)
		{
			case 'REGEXP':
				return new Regexp(null, $node['text']);

			case 'LITERAL':
				return new Literal(null, QuotedString::decode($node['text']));

			case 'BODY':
				return new Token(null, static::buildTokens($node['childNodes']));

			case 'CONDITION':
				return new Multiplier(null, static::buildTokens($node['childNodes']), true);

			case 'MULTIPLIER':
				return new Multiplier(null, static::buildTokens($node['childNodes']));

			case 'NAME':
				// I will take these cotton balls from you with my hand and put them in my pocket.
				// That is, here we replace name of a token with the actual token
				// (which we built from its definiton).
				// Unfortunately, we cannot just return getToken() here because of recursive definitions.
				// We need to return something NOW, and when all definitions are built we can
				// follow up and insert correct references to these temporary tokens.
				$token =& static::$followupTokens[$node['text']];
				if (!isset($token)) {
					$token = new Token(); // this actually creates an enormous amount of anonymous tokens
					static::getToken($node['text']); // make sure we build it
				}
				return static::$followupTokens[$node['text']];

			case 'ALTERNATIVE-TOKENS':
				return new Alternative(null, static::buildTokens($node['childNodes']));
		}

		throw new Exception('Cannot build grammar token ' . $type);
	}

	/**
	 * @param array $nodes
	 * @return Token[]
	 */
	private static function buildTokens($nodes)
	{
		$tokens = array();
		foreach($nodes as $node) {
			$tokens[] = static::buildTokenByTree($node);
		}
		return $tokens;
	}
}
