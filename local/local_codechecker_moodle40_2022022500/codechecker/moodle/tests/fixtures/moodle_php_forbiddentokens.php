<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Let's try various forbidden tokens (they are functions but the tokenizer handles them as special tokens).
$a = eval($b);
goto label;
exit(1);
label1:
echo 'Here we are, thanks to lovely goto';
exit(1);
label2: echo 'Will you understand ever that goto is forbidden?';
// Backticks aren't welcome as execution operators.
$files = `ls -al`;
// Fair enough.
