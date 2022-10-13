<?php

// Test skipping out early, no continue.
switch ($foo) {
    case 0:
        break;
    case 1:
        break;
}

/*
 * Test switch in combination with scoped control structures.
 */
switch ($foo) {
    case 0:
        continue; // Invalid.
    case 1:
        break;
}

while ($foo) {
    switch ($bar) {
        case 0:
            continue; // Invalid.
        case 1:
            continue 2;
        case 2:
            continue (1); // Invalid.
        case 3:
            continue 0; // Invalid. The fact that 0 is forbidden, is not the concern of this sniff.
        case 4:
            continue $var; // Undetermined. Outside the scope of this sniff.
        case 5:
            continue function_call(); // Undetermined. Outside the scope of this sniff.
        case 6:
            continue \E_WARNING; // Undetermined. Outside the scope of this sniff.
        case 7:
            continue (1 * 2);
        case 8:
            continue (1 * 1); // Invalid. The fact that calculations are no longer valid is not the concern of this sniff.
        case 9:
            continue (1 /* comment */ * 2);
        case 10:
            continue
                (
                    1 * 1
                ); // Invalid. The fact that calculations are no longer valid is not the concern of this sniff.
        case 11:
            continue function () { return 1; }; // Undetermined. Outside the scope of this sniff.
        case 12:
            continue 3; // Impossible level, not our concern.
    }
}

while ($foo) {
    switch ($bar) {
        case 0:
            while ($xyz) {
                continue 2; // Invalid.
            }
        case 1:
            while ($xyz) {
                continue 3;
            }
        case 2:
            while ($xyz) {
                break 2;
            }
    }
}

foreach ($foo as $k => $v) {
    switch ($k) {
        case 0:
            for ($i = 0; $i < $k; $i++) {
                if ($i === true) {
                    continue 2; // Invalid.
                } else {
                    continue; // Valid.
                }
            }
        case 1: {
            do {
                if ($a) {
                    continue 3; // Valid.
                } else {
                    continue 2; // Invalid.
                }
            } while ( $v );
        }
        case 2:
            if ($xyz) {
                continue 2; // Valid.
            } else {
                continue 1; // Invalid.
            }
        case 3:
            switch ($v) {
                case 'a':
                    continue; // Invalid.
                case 'b':
                    continue 2; // Invalid.
                case 'c':
                    continue 3; // Valid.
            }
    }
}

/*
 * Test switch in combination with unscoped and alternative control structures.
 */
switch ($foo):
    case 0:
        continue; // Invalid.
endswitch;

while ($foo)
    switch ($bar):
        case 0:
            continue; // Invalid.
        case 1:
            continue 2;
    endswitch;

while ($foo)
    switch ($bar):
        case 0:
            while ($xyz)
                continue 1;

        case 1:
            while ($xyz)
                continue 2; // Invalid, but ignored as it's in an unscoped control structure (false negative).

        case 2:
            while ($xyz)
                continue 3;
        case 3:
            while ($xyz)
                while ($yzx)
                    while ($zyx)
                        if ($a)
                            continue;
                        elseif ($b)
                            continue 4; // Invalid, but ignored as it's in an unscoped control structure (false negative).
                        elseif ($c)
                            continue 5;
        case 4:
            continue; // Invalid.
    endswitch;

foreach ($foo as $k => $v)
    switch ($k):
        case 0;
            for ($i = 0; $i < $k; $i++)
                continue 2; // Invalid, but ignored as it's in an unscoped control structure (false negative).

        case 1:
            do
                continue 3;
            while ( $v );
        case 2;
            if ($xyz)
                continue 2;
    endswitch;


/*
 * Test switch in combination with mixed scoped/unscoped/alternative control structures.
 */
while ($foo) {
    switch ($bar):
        case 0:
            continue; // Invalid.
        case 1:
            continue 2;
    endswitch;
}

while ($foo)
    switch ($bar) {
        case 0:
            while ($xyz)
                continue 2; // Invalid, but ignored as it's in an unscoped control structure (false negative).

        case 1: {
            while ($xyz) {
                continue 3;
            }
        }
    }
