<?php

// Pre-existing type casts.
(string) 1234;
(real) '1.5';

// Newly introduced type casts.
(unset) $a;
(binary) 1234;
$binary = b"binary string";
$binary = b"binary $string";
$also_binary = b'124';

// Verify space & case independency.
(	unset	) $a;
( binary ) 1234;
( Unset ) $a;
( BINARY ) 1234;

// Just making sure / no false positives.
$ordinary = 'b"something"';
