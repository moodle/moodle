<?php

/**
 * Valid examples - none of these should trigger an error.
 */
for ($i = 0; $i < 10; $i++) {
    if ($i === 5) {
        continue;
    }
    if ($i === 8) {
        break;
    }
}

foreach ($forExample as $key => $value) {
    if ($key === 5) {
        continue;
    }
    if ($key === 8) {
        break;
    }
}

while ($whileExample < 10) {
    if ($whileExample === 5) {
        continue;
    }
    if ($whileExample === 8) {
        break;
    }
    $whileExample++;
}

do {
    if ($doWhileExample === 5) {
        continue;
    }
    if ($doWhileExample === 8) {
        break;
    }
    $doWhileExample++;
} while ($doWhileExample < 10);

switch ($switchKey) {
    case 5:
        echo 'hello';
        continue;

    case 8:
        echo 'world';
        break;

    default:
        break;
}

// Alternative syntax for control structures.
for ($i = 0; $i < 10; $i++):
    if ($i === 5):
        continue;
    endif;
    if ($i === 8):
        break;
    endif;
endfor;

foreach ($forExample as $key => $value):
    if ($key === 5):
        continue;
    endif;
    if ($key === 8):
        break;
    endif;
endforeach;

while ($whileExample < 10):
    if ($whileExample === 5):
        continue;
    endif;
    if ($whileExample === 8):
        break;
    endif;
    $whileExample++;
endwhile;

switch ($switchKey):
    case 5:
        echo 'hello';
        continue;

    case 8:
        echo 'world';
        break;

    default:
        break;
endswitch;

// Control structure within a function.
function testingScope() {
    for ($i = 0; $i < 10; $i++) {
        if ($i === 5) {
            continue;
        }
        if ($i === 8) {
            break;
        }
    }
}


/**
 * Invalid examples - these should all trigger an error.
 */
if ( $a === $b ) {
    continue;
} elseif ( $a === $c ) {
    continue;
} else {
    break;
}

function testFunctionA() {
    continue;
}

function testFunctionB() {
    break;
}

continue;
