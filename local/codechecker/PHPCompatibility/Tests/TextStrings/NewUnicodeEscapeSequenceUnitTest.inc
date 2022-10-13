<?php

// This is fine cross-version.
json_decode("\"\u202e\"");
echo 'single quoted: \u{aa}';
echo "This is a text to explain the escape sequence which looks like \\u{9999} (escaped)";

$location_vars = array("c\u0327ity", "state");
var_dump("\u");
var_dump("\ufoobar");

echo 'multi-line
"text \u{aa} text"
string with quotes inside';

// This will not be recognized as an escape sequence, i.e. no space allowed between the \u and the {.
echo "\u
{1F602}";

/*
 * PHP 7.0: Sequences which resemble unicode escape sequences, but are not
 * and will now throw a parse error.
 *
 * Unit tests largely taken from the PHP native merged PR.
 */
// This will be a parse error in PHP 7.0.
echo "\u{foobar"; // Incomplete.
echo "\u{9999"; // Incomplete.
echo "\u{}"; // Empty.
var_dump("\u{+1F602}"); // Positive sign is not allowed.
var_dump("\u{-1F602}"); // Negative sign is not allowed.
var_dump("\u{1F602 }"); // Whitespace is not allowed.

// Parse error: This is outside of the maximum permissable range.
echo "\u{110000}"; // U+10FFFF + 1

/*
 * PHP 7.0: Unicode codepoint escape sequences.
 * Unit tests largely taken from the RFC.
 */
echo "\u{aa}";
echo <<<EOD
Some text \u{0000aa}
and some more \u{9999} text
EOD;
echo "\u{9999}";
echo "ma\u{00F1}ana";
echo "man\u{0303}ana";
echo "\u{1F602}";

echo "multi-line
'text \u{aa} text'
string with quotes inside";

echo "\u{202E}Reversed text";

// Surrogate pairs are non-well-formed UTF-8 - however, it is sometimes useful
// to be able to produce these (e.g. CESU-8 handling)
var_dump(bin2hex("\u{D801}"));
var_dump(bin2hex("\u{DC00}"));
var_dump(bin2hex("\u{D801}\u{DC00}")); // CESU-8 encoding of U+10400
