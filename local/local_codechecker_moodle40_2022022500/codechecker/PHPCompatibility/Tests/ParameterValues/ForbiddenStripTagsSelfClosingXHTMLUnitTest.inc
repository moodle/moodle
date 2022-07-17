<?php

// OK.
$str = strip_tags($str);
$str = strip_tags($input, '<br>');
$str = strip_tags($input, '<img><br><meta><input>');

// Undetermined. Ignore.
$str = strip_tags($str, $allowable_tags);
$str = strip_tags($str, self::ALLOWABLE_TAGS);
$str = strip_tags($str, MyClass::get_allowable_tags('<br/>'));

// Not OK - warning.
$str = strip_tags($input, '<br/>');
$str = strip_tags($input, '<img/><br/>' . '<meta/><input/>');
