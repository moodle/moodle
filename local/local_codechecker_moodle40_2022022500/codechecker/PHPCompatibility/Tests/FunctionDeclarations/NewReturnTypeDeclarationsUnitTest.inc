<?php

// PHP 7.0+
function fooBool($a): bool {}
function fooInt($a): int {}
function fooFloat($a): float {}
function fooString($a): string {}
function fooArray($a): array {}
function fooCallable($a): callable {}
function fooSelf($a): self {}
function fooParent($a): parent {}
function fooBaz($a): Baz {}
function fooGNSBaz($a): \Baz {}
function fooNSBaz($a): myNamespace\Baz {}
function fooNSBaz2($a): \myNamespace\Baz {}

// PHP 7.1+
function fooIterable($a): iterable {}
function fooVoid($a): void {}

// Anonymous function.
function($a): callable {}

// OK: no return type hint.
function fooNone($a) {}
function ($a) {}

// PHP 7.2+
function fooObject($a): object {}

function fooInterspersedWithComments($a) :
	// Comment.
	?
	// phpcs:ignore Standard.Category.Sniff -- ignore something about a return type declaration.
	\myNamespace\
	// Comment.
	Baz
{
}
