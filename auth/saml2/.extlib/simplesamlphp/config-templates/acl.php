<?php

/*
 * This file defines "named" access control lists, which can
 * be reused in several places.
 */
$config = [
    'adminlist' => [
        //['allow', 'equals', 'mail', 'admin1@example.org'],
        //['allow', 'has', 'groups', 'admin'],
        // The default action is to deny access.
    ],

    'example-simple' => [
        ['allow', 'equals', 'mail', 'admin1@example.org'],
        ['allow', 'equals', 'mail', 'admin2@example.org'],
        // The default action is to deny access.
    ],

    'example-deny-some' => [
        ['deny', 'equals', 'mail', 'eviluser@example.org'],
        ['allow'], // Allow everybody else.
    ],

    'example-maildomain' => [
        ['allow', 'equals-preg', 'mail', '/@example\.org$/'],
        // The default action is to deny access.
    ],

    'example-allow-employees' => [
        ['allow', 'has', 'eduPersonAffiliation', 'employee'],
        // The default action is to deny access.
    ],

    'example-allow-employees-not-students' => [
        ['deny', 'has', 'eduPersonAffiliation', 'student'],
        ['allow', 'has', 'eduPersonAffiliation', 'employee'],
        // The default action is to deny access.
    ],

    'example-deny-student-except-one' => [
        ['deny', 'and',
            ['has', 'eduPersonAffiliation', 'student'],
            ['not', 'equals', 'mail', 'user@example.org'],
        ],
        ['allow'],
    ],

    'example-allow-or' => [
        ['allow', 'or',
            ['equals', 'eduPersonAffiliation', 'student', 'member'],
            ['equals', 'mail', 'someuser@example2.org'],
        ],
    ],

    'example-allow-all' => [
        ['allow'],
    ],
];
