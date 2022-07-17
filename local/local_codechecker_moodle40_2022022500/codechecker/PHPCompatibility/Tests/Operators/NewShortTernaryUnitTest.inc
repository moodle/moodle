<?php
$a = $b = $c = 0;

// Always OK:
$a ? $b : $c;

// Only in 5.3 and above:
$a ?: $c;

$isError = ($function != 'ini_get') ?: false;
