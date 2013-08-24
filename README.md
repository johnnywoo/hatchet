# Hatchet: a simple grammar-based parser

## UBER-DISCLAIMER

This is a work in progress. Everything will change a lot. If I ever find
the time to do it, which is not guaranteed at all.

## DISCLAIMER

Hatchet is not meant to be fast, it's a simple recursive descent parser.
The primary goals of Hatchet are simple grammar syntax and a concise API.
In other words, if you want speed, you should cache all the things.
A loaded grammar object can be safely serialized and reused to skip
parsing and building of the grammar file.

Error reporting is nonexistent, as any parsing error will be reported as
'root token does not cover the whole file'.

Also, the codebase must be pretty unstable, there being no production
application of it.

## HOWTO

    <?php
	require_once 'hatchet/lib/autoload.php';
    $grammar = new \Hatchet\Grammar(file_get_contents('your-grammar.hatchet'));
    $tree = $grammar->parse($text);
    ?>

## GRAMMAR SYNTAX

A grammar is a list of token definitions. You can look at hatchet.hatchet
for an example (that is actually a grammar of the grammar itself).

Comments in a grammar are lines starting with `#`. There are no multiline
comments at the moment.

### Example: INI file grammar

First of all, we need to define a root token. Token without a name will be used
as the root one. It must cover the whole parsed file. Our INI file is a list of
sections, which all have names, except possibly the first one (it can be
anonymous).

    # INI file is a collection of sections
    : [anon-section] {section}

Curly braces mean 'repeat any number of times, including zero'; square brackets
mean 'may be present or not'. The words 'anon-section' and 'section' are names
of tokens, which we are going to describe next. Order of token definitions is
not important, but it is usually easier to read the grammar if you write them
in order in which they include each other.

	anon-section: section-body
    section: "[" section-name "]" eol section-body
    # sections are made of definitions and empty lines
    section-body: {[name "=" value] eol}

This should be self-explanatory. Now we have described pretty much all of the
structure. The rest of tokens can be quite easily defined with regular
expressions:

	# names are just some word-chars
    section-name: /\w+/
    name: /\w+/
    # values can be anything that fits into one line
    value: /[^\r\n]*/
    # a newline char or the end of file
    eol: /(\r?\n|$)/

Look into hatchet.hatchet for additional syntax constructs (grouping,
alternatives).

### Whitespace

In order to unclutter the grammar definition, Hatchet allows inline whitespace
(spaces and tabs) between any tokens. This way you don't have to manually
insert whitespace tokens everywhere.

You many switch between whitespace modes using `@whitespace` declaration.
There may be only one whitespace declaration per grammar.

 1. `manual`: no implicit whitespace at all
 2. `inline`: implicit inline whitespace (current behaviour, useful for
    line-based config files)
 3. `implicit`: Implicit whitespace including line breaks (useful for XML-like
    syntaxes)

### Special tokens

`_quoted_` is a special (predefined) token for a quoted string, i.e.
"line \n other line" or 'whatever' (with quotes included in the matched text).
Escaping inside quoted strings: `\r` `\n` `\t` `\xAB` `\\` and
also `\"` or `\'` depending which quotes you are in. Invalid escape sequences
are left as they were.

`_whitespace_` is a special token for an inline whitespace character (space or
tab). It is useful if you want to force at least one space/tab between your
tokens. Hatchet by default allows any whitespace between tokens, including none.

## TODO

 * Parse-time callbacks (maybe? non-intuitive)
 * Maybe replace `_whitespace_` with normal regexp tokens
 * A proper readme
 * Examples with simple formats (ini, css, yaml)
 * Inline regex literals
 * Make the grammar grammar CRLF-friendly
 * Investigate friendly syntax errors with line numbers
 * Investigate performance and memory usage
 * Make an example for expression parsing and operator precedence (calculator)
 * (@named "groups")? Could be useful to assign same name to different literals
 * Super challenge: SQL
 * Includes (and a standard lib like math expressions?)
 * Set manual whitespace as default
 * Multiline token definition
 * Easy universal test: XMLize text (wrap given text in <Token></Token>)
 * Change tabs into spaces and generally change all formatting
 * Annotations for token definitions
 * A way to specify whitespace mode for a token (annotations)
 * A way to locally disable implicit whitespace (annotations)
 * Move uppercase convention into Hatchet proper
 * Replace array responses with objects
 * Lookup namespaces
 * Nested indent block annotations

We can make callbacks with signature process_child($node, $child)
and probably will be able to start from root and then go deep.

What are the use cases?

 1. Read a LESS file, convert it into CSS
 2. Read INI file into a variable

We can implement callbacks like this:
take a compiler object
when a childless token is scanned, call compiler->token()
when a token group starts, call compiler->begin()
when a token group ends, call compiler->end()
(or even drop the token() and just call begin() and end() with positions)
but how can we do this with backtracking? we should not call begin() if the
token is going to be discarded

Annotations can be used for doing things with the grammar.
E.g. @anonymize-if-one-child would inline a token if it only has one child
(useful for grouping.multiplication.addition.negative.number).
Root token annotations could be treated as global (whitespace modes etc),
then whitespace mode can be updated for the particular token tree.

Other possibility is to make custom ASTs. So we'll have a notation that
tokens starting with an uppercase letter are 'collected', while others
are flattened. Then we set up some correlation between token names and
classes (lookup namespace, direct mapping, whatever). So we have a catered
tree of proper objects right out of the parser.
