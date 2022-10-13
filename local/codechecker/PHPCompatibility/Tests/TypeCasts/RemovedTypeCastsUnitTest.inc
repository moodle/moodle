<?php

// Some other type casts.
(string) 1234;
(float) '1.5';

// Deprecated unset type cast.
(unset) $a;

// Verify space & case independency.
(	unset	) $a;
( Unset ) $a;

// Deprecated real type cast.
$a = (real) '1.5';
$a = ( real ) '1.5';
$a = (double) '1.5'; // Make sure this doesn't throw a false positive.
