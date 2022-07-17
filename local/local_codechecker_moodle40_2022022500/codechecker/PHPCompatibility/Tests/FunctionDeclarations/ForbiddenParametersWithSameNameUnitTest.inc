<?php

function foo($a, $b, $unused, $unused) { }

function foobaz() {} // No parameters = no error.

function ($a, $b, $unused, $unused) {}; // Anonymous function with params of same name.

function varsAreCaseSensitive( $a, $A ) {}

// Don't throw errors during live code review.
function foobar($a,$a
