<?php

for ($i = 0; $i < 20; $i++) {
    for ($j = 0; $j < 5; $j++) {

        // OK: Simple break/continue.
        if ($i == 1) {
            break;
        }

        if ($i < 20) {
            continue;
        }

        // OK: Break/continue with integer.
        if ($i == 2) {
            break 1;
        }

        if ($i < 19) {
            continue 1;
        }

        // OK: Break/continue with some random (namespaced) constant.
        if ($i == 5) {
            break E_WARNING; // Just some random constant.
        }

        if ($i < 16) {
            continue \E_WARNING;
        }

        // OK: Break/continue with a calculation.
        if ($i == 6) {
            break (1 + 1);
        }

        if ($i < 15) {
            continue (1 * 2);
        }

        // OK: Break/continue with cast and a (namespaced) constant.
        if ($i == 9) {
            break (int) MyNamespace\E_WARNING;
        }

        if ($i < 12) {
            continue (int) E_WARNING;
        }

        // Bad: Break/continue with variable.
        if ($i == 3) {
            break $num; // Bad.
        }

        if ($i < 18) {
            continue $num; // Bad.
        }

        // Bad: Break/continue with function call.
        if ($i == 4) {
            break rand(); // Bad.
        }

        if ($i < 17) {
            continue rand(); // Bad.
        }

        // Bad: Break/continue with a calculation using a variable.
        if ($i == 7) {
            break 1 + $num; // Bad.
        }

        if ($i < 14) {
            continue 1 + $num; // Bad.
        }

        // Bad: Break/continue with a cast and a variable.
        if ($i == 8) {
            break (int) $i; // Bad.
        }

        if ($i < 13) {
            continue (int) $i; // Bad.
        }

        // Bad: Break/continue with a closure.
        if ($i == 10) {
            break function () { return 1; }; // Bad.
        }

        if ($i < 11) {
            continue function () { return 1; }; // Bad.
        }

        // Bad: Break/continue with a namespaced function call.
        if ($i == 11) {
            break MyNamespace\myFunction(); // Bad.
        }

        if ($i < 10) {
            continue MyNamespace\myFunction(); // Bad.
        }

        // Bad: Break/continue with zero value.
        if ($i == 1) {
            break 0;
        }

        if ($i < 20) {
            continue 0;
        }
    }
}

// Issue #460 and some variations.
for ($x=0;$x<5;$x++):
    continue 0 ?> <?php
endfor;

for ($x=0;$x<5;$x++):
    continue $x ?> <?php
endfor;

for ($x=0;$x<5;$x++):
    continue ?> <?php
    print 0;
endfor;
