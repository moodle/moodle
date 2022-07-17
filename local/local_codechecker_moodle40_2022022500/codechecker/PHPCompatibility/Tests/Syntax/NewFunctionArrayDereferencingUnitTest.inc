<?php

echo test()[0]; // Error.

echo test()->property[2]; // Ok.

// Issue #226 - these are all Ok.
print '"' . dol_trunc($companystatic->name, 16) . ' - ' . $val["refsuppliersologest"] . ' - ' . dol_trunc($accountingaccount->label, 32) . '"' . $sep;
if (dol_strlen(trim($this->object->address)) == 0) $this->tpl['address'] = $objsoc->address;
dol_htmloutput_errors((is_numeric($GLOBALS['error'])?'':$GLOBALS['error']),$GLOBALS['errors']);
if ((! is_numeric($records) || $records != 0) && (! isset($records['count']) || $records['count'] > 0)) {}

// Issue #227 - these should all throw an error.
echo $foo->bar()[1];
echo $foo->bar()->baz()[2];
echo testClass::another_test()[0];

/*
 * PHP 7.0: function array dereferencing using curly braces.
 * This "silently" started working in PHP 7.0. See: https://3v4l.org/a1TW6
 */
echo test(){0};
echo $foo->bar(){1};
echo $foo->bar()->baz(){2};
echo testClass::another_test(){0};

// Mixing access with square brackets and curly braces.
echo $foo->bar()->baz()[0]{2}; // Should give two errors (depending on supported PHP version).
echo $foo->bar()->baz(){0}[2]; // Should give two errors (depending on supported PHP version).

// Prevent curly braces false positive on function declared by reference.
function &test() {
	echo '123';
}

// Don't throw errors during live code review.
echo test(
