<?php

$Source = "anything";
$Replace = "anything";

///////////// No warning generated:

// Different quote styles.
preg_replace("/double-quoted/", $Replace, $Source);
preg_replace('/single-quoted/', $Replace, $Source);

// Different regex markers.
preg_replace('#hash-chars (common)#', $Replace, $Source);
preg_replace('!exclamations (why not?!', $Replace, $Source);

// Safe modifiers
preg_replace('/some text/mS', $Replace, $Source);
preg_replace('#some text#gi', $Replace, $Source);

// E modifier doesn't exist, but should not trigger error.
preg_replace('//E', $Replace, $Source);

// Multi-line example (issue #83)
$text = preg_replace(
    '/(?<!\\\\)     # not preceded by a backslash
      <             # an open bracket
      (             # start capture
        \/?         # optional backslash
        collapse    # the string collapse
        [^>]*       # everything up to the closing angle bracket; note that you cannot use one inside the tag!
      )             # stop capture
      >             # close bracket
    /ix',
    '[$1]',
    $text
  );

// Multi-line with /e in comments.
preg_replace(
        '/.*     # /e in a comment
        /x',
    $Replace, $Source);

// Escaped /e
preg_replace('/\/e/', $Replace, $Source);

///////////// Warning generated:

// Different quote styles.
preg_replace("/double-quoted/e", $Replace, $Source);
preg_replace('/single-quoted/e', $Replace, $Source);

// Different regex markers.
preg_replace('#hash-chars (common)#e', $Replace, $Source);
preg_replace('!exclamations (why not?!e', $Replace, $Source);

// Other modifiers with /e
preg_replace('/some text/emS', $Replace, $Source);
preg_replace('/some text/meS', $Replace, $Source);
preg_replace('/some text/mSe', $Replace, $Source);

// Multi-line example (issue #83)
$text = preg_replace(
    '/(?<!\\\\)     # not preceded by a backslash
      <             # an open bracket
      (             # start capture
        \/?         # optional backslash
        collapse    # the string collapse
        [^>]*       # everything up to the closing angle bracket; note that you cannot use one inside the tag!
      )             # stop capture
      >             # close bracket
    /iex',
    '[$1]',
    $text
  );

// Multi-line with /e in comments.
preg_replace(
        '/.*     # /e in a comment
        /xe',
    $Replace, $Source);

// Escaped /e
preg_replace('/\/e/e', $Replace, $Source);

///////////// Untestable - should not generate an error.

$Regex = "/anything/";
define("X_REGEX_Xe", "/anything/");
function XRegeXe() {
    return "/anything/";
}

preg_replace($Regex, $Replace, $Source);
preg_replace(XRegeXe(), $Replace, $Source);
preg_replace(X_REGEX_Xe, $Replace, $Source);

///////////// Using bracket delimiters
preg_replace('{\d}e', $Replace, $Source); //bad
preg_replace('{\d{2}}e', $Replace, $Source); //bad
preg_replace('{\d{2}e}', $Replace, $Source); //good
preg_replace('`\d{2}e`', $Replace, $Source); // good
preg_replace('{^fopen(.*?): }', $Replace, $Source); //good - monolog example
preg_replace('[\d{2}]e', $Replace, $Source); //bad
preg_replace('[\d{2}e]', $Replace, $Source); //good
preg_replace('(\d{2})e', $Replace, $Source); //bad
preg_replace('(\d{2}e)', $Replace, $Source); //good
preg_replace('<\d{2}>e', $Replace, $Source); //bad
preg_replace('<\d{2}e>', $Replace, $Source); //good

///////////// Using preg_filter

// Different quote styles.
preg_filter("/double-quoted/e", $Replace, $Source);
preg_filter('/single-quoted/e', $Replace, $Source);

// Different regex markers.
preg_filter('#hash-chars (common)#e', $Replace, $Source);
preg_filter('!exclamations (why not?!e', $Replace, $Source);

// Other modifiers with /e
preg_filter('/some text/emS', $Replace, $Source);
preg_filter('/some text/meS', $Replace, $Source);
preg_filter('/some text/mSe', $Replace, $Source);

// Multi-line example (issue #83)
$text = preg_filter(
    '/(?<!\\\\)     # not preceded by a backslash
      <             # an open bracket
      (             # start capture
        \/?         # optional backslash
        collapse    # the string collapse
        [^>]*       # everything up to the closing angle bracket; note that you cannot use one inside the tag!
      )             # stop capture
      >             # close bracket
    /iex',
    '[$1]',
    $text
  );

// Multi-line with /e in comments.
preg_filter(
        '/.*     # /e in a comment
        /xe',
    $Replace, $Source);

// Escaped /e
preg_filter('/\/e/e', $Replace, $Source);

///////////// More warning generated:

// Regex build up of strings combined with variables/function calls.
preg_replace('/something' . $variable . 'something else/e', $Replace, $Source);
preg_replace('/something' . preg_quote($variable) . 'something else/e', $Replace, $Source);

// Issue 265 - build up string with varying quotes - this should be ok.
preg_replace('~'.testme()."~s", '', "foo bar was here");

// Passing an array of patterns:
preg_replace(
    array(
        "/double-quoted/e",
        '/single-quoted/e',
        '#hash-chars (common)#e',
        '!exclamations (why not?!e',
        '/some text/emS',
        '/some text/i', // Ok
    ), $Replace, $Source
);
// Array of patterns with array keys:
preg_replace(
    [
        'ae' => "/double-quoted/e",
        'be' => '/single-quoted/e',
        'ce' => '#hash-chars (common)#e',
        'de' => '!exclamations (why not?!e',
        'ee' => '/some text/emS',
        'ef' => '/some text/i', // Ok
    ], $Replace, $Source
);
// Single line array.
preg_replace(array("/double-quoted/e", '/something'.preg_quote($variable, '/').'something else/e', '/single-quoted/e',), $Replace, $Source);


// Array of patterns, check against false positive - this should be ok.
// https://wordpress.org/support/topic/wrong-error-preg_replace-e-modifier-is-forbidden-since-php-7-0/
$cleaned = preg_replace(
  array(
    '/<!--[^\[><](.*?)-->/s',
    '#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre)\b))*+)(?:<(?>textarea|pre)\b|\z))#'
  ),
  array(
    '',
    ' '
  ),
  $data
);

// Another false positive.
// https://wordpress.org/support/topic/wrong-error-preg_replace-e-modifier-is-forbidden-since-php-7-0/
$this->value = preg_replace( $this->field['preg']['pattern'], $this->field['preg']['replacement'], $this->value );

// Deal correctly with interpolated strings.
preg_replace("/dou$ble-quoted/e", $Replace, $Source); // Bad.
preg_replace("/dou$ble-quoted/me$me", $Replace, $Source); // Bad.
preg_replace("/double-quoted/$e", $Replace, $Source); // Ok.

// Yet another false positive. Who the heck uses a quote character as a delimiter ?!?!?!?
// https://wordpress.org/support/topic/false-positive-preg_replace-e-modifier/
$code = preg_replace("'(?<![\$.a-zA-Z0-9_])while\('", '3#(', $code); // Ok.
$code = preg_replace("'(?<![\$.a-zA-Z0-9_])while\('e", '3#(', $code); // Bad.
