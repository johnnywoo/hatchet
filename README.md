= Hatchet: a simple grammar-based parser =

== Grammar syntax ==

A grammar is a list of token definitions. Look at hatchet.hatchet for and example
(that is actually a grammar of the grammar itself).

Comments in a grammar are lines starting with `#`. There are no multiline comments at the moment.

=== Special tokens ===

`_quoted_` is a special (predefined) token for a quoted string, i.e. "line \n other line" or 'whatever'
(with quotes included in the matched text). These strings follow PHP style of quoting for special characters.