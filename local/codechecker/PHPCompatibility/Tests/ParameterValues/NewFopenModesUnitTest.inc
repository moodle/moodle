<?php

// OK.
$handle = fopen("/home/rasmus/file.txt", "r");
$handle = fopen("/home/rasmus/file.gif", 'wb');
$handle = fopen("/home/rasmus/file.gif", 'a' . $additional_modes);

// Not OK.
$handle = fopen("/home/rasmus/file.txt", "re"); // PHP 7.0.16+
$handle = fopen("/home/rasmus/file.gif", 'c+'); // PHP 5.2.6+
$handle = fopen("/home/rasmus/file.gif", 'c'); // PHP 5.2.6+
$handle = fopen("/home/rasmus/file.gif", 'c'.'e'); // PHP 5.2.6+ and PHP 7.0.16+
