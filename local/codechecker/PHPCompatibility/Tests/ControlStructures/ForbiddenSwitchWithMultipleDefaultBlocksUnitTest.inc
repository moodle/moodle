<?php

switch ($something) {
    case 1:
        break;
    default:
        break;
    case 2:
        break;
    default:
        break;
}

switch ($something) {
    case 1:
        break;
    default:
        break;
    case 2:
        break;
}

switch ($something) {
    case 1:
        break;
    case 2:
        break;
    default:
        break;
}


switch ($foo) {
    case 'a':
        switch ($bar) {
            case 'b':
                echo 'b';
                break;
            default:
                echo 'x';
        }
        break;
    default:
        echo "x\n";
}

switch ($something) {
    case 1:
    default:
        break;
    case 2:
    default:
        break;
}

switch ($something) {
    case 1:
        break;
    case 2:
        break;
    default:
    default:
        break;
}

// Don't throw errors on live code review.
switch ($something) {
