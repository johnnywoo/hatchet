# Hatchet: a simple grammar-based parser

## DISCLAIMER

Hatchet is not meant to be fast, it's a simple recursive descent parser.
The primary goals of Hatchet are simple grammar syntax and a concise API.
In other words, if you want speed, you should cache all the things.
A loaded grammar object can be safely serialized and reused to skip
parsing and building of the grammar file.

Also, the codebase must be pretty unstable, there being no production
application of it.

## HOWTO

    <?
	require_once 'hatchet/lib/autoload.php';
    $grammar = new \hatchet\Grammar(file_get_contents('your-grammar.hatchet'));
    $tree = $grammar->parse($text);
    ?>

## GRAMMAR SYNTAX

A grammar is a list of token definitions. You can look at hatchet.hatchet
for an example (that is actually a grammar of the grammar itself).

Comments in a grammar are lines starting with `#`. There are no multiline comments at the moment.

### Example: INI file grammar

First of all, we need to define a root token. Token without a name will be used as the root one.
It must cover the whole parsed file. Our INI file is a list of sections, which all have names,
except possibly the first one (it can be anonymous).

    # INI file is a collection of sections
    : [anon-section] {section}

Curly braces mean 'repeat any number of times, including zero'; square brackets mean 'may be present or not'.
The words 'anon-section' and 'section' are names of tokens, which we are going to describe next.
Order of token definitions is not important, but it is usually easier to read the grammar if
you write them in order in which they include each other.

	anon-section: section-body
    section: "[" section-name "]" eol section-body
    # sections are made of definitions and empty lines
    section-body: {[name "=" value] eol}

This should be self-explanatory. Now we have described pretty much all of the structure.
The rest of tokens can be quite easily defined with regular expressions:

	# names are just some word-chars
    section-name: /\w+/
    name: /\w+/
    # values can be anything that fits into one line
    value: /[^\r\n]*/
    # a newline char or the end of file
    eol: /(\r?\n|$)/

Look into hatchet.hatchet for additional syntax constructs (grouping, alternatives).

### Whitespace

In order to unclutter the grammar definition, Hatchet allows inline whitespace
(spaces and table) between any tokens. This way you don't have to manually insert
whitespace tokens everywhere.

In future, there might be a possibility to switch between three whitespace modes:

 1. No implicit whitespace at all
 2. Implicit inline whitespace (current behaviour, useful for line-based config files)
 3. Implicit whitespace including line breaks (useful for XML-like syntaxes)

### Special tokens

`_quoted_` is a special (predefined) token for a quoted string, i.e. "line \n other line" or 'whatever'
(with quotes included in the matched text). These strings follow PHP style of quoting for special characters.

`_whitespace_` is a special token for an inline whitespace character (space or tab). It is useful if you want to force at least one
space/tab between your tokens. Hatchet by default allows any whitespace between tokens, including none.

## TODO

 * Proper quote scanning
 * Normal quote processing instead of eval
 * Parse-time callbacks
 * Whitespace modes
 * Probably a way to specify whitespace mode for a token
 * Maybe replace _whitespace_ with normal regexp tokens
 * A proper readme
 * Examples with simple formats (ini, css)
 * Inline regex literals
 * Make the grammar grammar CRLF-friendly
 * Investigate friendly syntax errors with line numbers
 * Investigate performance and memory usage
 * Make an example for expression parsing and operator precedence (calculator)
 * (@named "groups")? Could be useful to assign same name to different literals

We can make callbacks with signature process_child($node, $child)
and probably will be able to start from root and then go deep.

What are the use cases?

 1. Read a LESS file, convert it into CSS
 2. Validate WTF tag nesting
 3. Read INI file into a variable