<?php

/* Case 1 */
new DateTime;


namespace MyTesting;

/* Case 2 */
new namespace\DateTime();
/* Case 3 */
new DateTime;
/* Case 4 */
new \DateTime();
/* Case 5 */
new anotherNS\DateTime();
/* Case 6 */
new \FQNS\DateTime();


namespace AnotherTesting {
    /* Case 7 */
    new namespace\DateTime();
    /* Case 8 */
    new DateTime;
    /* Case 9 */
    new \DateTime();
    /* Case 10 */
    new anotherNS\DateTime();
    /* Case 11 */
    new \FQNS\DateTime();
}

/* Case 12 */
new DateTime;
/* Case 13 */
new \DateTime;
/* Case 14 */
new \AnotherTesting\DateTime();


// Variant on issue #205.
$className = 'DateTime';
/* Case 15 */
new $className;


// Issue #338 - no infinite loop on unfinished code.
/* Case 16 */
$var = new
