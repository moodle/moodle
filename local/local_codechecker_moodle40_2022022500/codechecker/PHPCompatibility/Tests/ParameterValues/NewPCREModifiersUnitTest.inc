<?php

preg_match('/some text/mJ', $subject);
preg_grep('#some text#Ji', $input, $flags);

$text = preg_match_all(
    '/(?<!\\\\)     # not preceded by a backslash
      <             # an open bracket
      >             # close bracket
    /iJx',
    '[$1]',
    $text
  );

preg_split(
    [
        'be' => '/single-quoted/J',
        'ce' => '#hash-chars (common)#j',
        'de' => '!exclamations (why not?!eJs',
    ], $subject, 2
);

preg_replace_callback_array(
    [
        '~[a]+~J' => function ($match) {
            echo strlen($match[0]), ' matches for "a" found', PHP_EOL;
        },
        '~[b]+~i' => function ($match) {
            echo strlen($match[0]), ' matches for "b" found', PHP_EOL;
        }
    ],
    $subject
);
