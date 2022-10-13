<?php

const ABC = ['a', 'b'];
const AB = array('a', 'b');

const ANIMALS = [
    'dog',
    'cat',
    'bird'
];

const MORE_ANIMALS = array(
    'dog',
    'cat',
    'bird'
);

class MyClass {
    const ANIMALS = [
        'dog',
        'cat',
        'bird'
    ];

    const MORE_ANIMALS = array( 'dog', 'cat', 'bird' );
}

/*
 * Minimal tests against false positives.
 */
const ANIMALS = 'array';

const DEF; // Not an assignment. Useless, but what the heck ;-)

// Multi-constant declaration.
const MULTI_A = 1,
    MULTI_B = array( 'a', 'b' ),
    MULTI_C = 'string',
    MULTI_D = ['a', 'b'],
    MULTI_E = true,
    MULTI_F = array(
        ['c', 'd'],
    );
