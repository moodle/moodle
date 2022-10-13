<?php

// OK.
$str = strip_tags($str);
$str = strip_tags($input, '<a><p>');

// Undetermined. Ignore.
$str = strip_tags($str, $allowable_tags);
$str = strip_tags($str, self::ALLOWABLE_TAGS);
$str = strip_tags($str, MyClass::get_allowable_tags(['a', 'p']));

// PHP 7.4: passing $allowable_tags as an array.
$str = strip_tags($input, ['a', 'p']);
$str = strip_tags(
    $input,
    array(
        'a',
        'p'
    )
);

// PHP 7.4: Warning. Incorrectly passing $allowable_tags as an array.
$str = strip_tags($input, ['<a>', '<p>']);
$str = strip_tags(
    $input,
    array(
        '<a>',
        '<p>'
    ),
);

// Prevent false positive.
$str = strip_tags($input, ['br', ...function_call('<a>,<p>')]); // PHP 7.4 syntax: unpacking within an array.
$str = strip_tags($input, []);
