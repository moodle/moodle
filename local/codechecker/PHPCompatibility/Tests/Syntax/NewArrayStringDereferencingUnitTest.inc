<?php

// PHP 5.5: Array and string literal dereferencing.
echo array(1, 2, 3)[0];
echo [1, 2, 3][0];
echo [1, [20, 21, 22], 3][1][1]; // Should give two errors.
echo 'PHP'[0];
echo "PHP"[0];

// Check against false positives.
echo [1, 2, 3];
echo [1, [20, 21, 22], 3];
echo 'PHP';
echo "P{$H}P"[0]; // Parse error.
echo $array[0];
echo $string[0];

// PHP 7.0: Array and string literal dereferencing using curly braces.
// This "silently" started working in PHP 7.0. See: https://3v4l.org/SlXd2
echo array(1, 2, 3){0};
echo [1, 2, 3]{0};
echo [1, [20, 21, 22], 3]{1}{1}; // Should give two errors.
echo 'PHP'{0};
echo "PHP"{0};

// Mixing access with square brackets and curly braces.
echo [1, [20, 21, 22], 3][1]{1}; // Should give two errors (depending on supported PHP version).
echo [1, [20, 21, 22], 3]{1}[1]; // Should give two errors (depending on supported PHP version).
