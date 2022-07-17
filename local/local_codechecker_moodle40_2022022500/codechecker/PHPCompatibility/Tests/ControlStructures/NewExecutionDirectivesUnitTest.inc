<?php

/*
 * The below directives are valid.
 */
declare(ticks=1);
declare ( ticks = 1 ) {} // Test with varying spacing.
declare(encoding='ISO-8859-1');
declare(strict_types=1) {
    $var = false;
}

/*
 * The below directives have invalid values.
 */
declare(ticks=TICK_VALUE); // Invalid - only literals may be given as directive values.
declare(encoding='invalid'); // Invalid - not a valid encoding.
declare(strict_types=false); // Invalid - only 1 is a valid value.


// Invalid directive name.
declare(invalid=true);

// Incomplete directive.
declare(
