<?php

/*
 * Test these two cases separately as they trigger a bug in PHPCS 2.5.1.
 * Ref: https://github.com/squizlabs/PHP_CodeSniffer/commit/9a70ae2d4c0a0bd0f48b965202158defb828cadd
 */

// Live coding.
$f = function ($param) use

// Live coding.
$f = function ($param) use ($param
