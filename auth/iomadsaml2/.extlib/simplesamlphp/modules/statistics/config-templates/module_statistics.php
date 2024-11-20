<?php
/*
 * The configuration of SimpleSAMLphp statistics package
 */

$config = [
    // Authentication & authorization for statistics

    // Whether the statistics require authentication before use.
    'protected' => false,

    // The authentication source that should be used.
    'auth' => 'admin',

    // Alternative 1: List of allowed users.
    //'useridattr' => 'eduPersonPrincipalName',
    //'allowedUsers' => array('andreas@uninett.no', 'ola.normann@sp.example.org'),

    // Alternative 2: External ACL list.
    //'acl' => 'adminlist',

    'default' => 'sso',

    'statdir' => '/tmp/stats/',
    'inputfile' => '/var/log/simplesamlphp.stat',
    'offset' => 60 * 60 * 2 + 60 * 60 * 24 * 3, // Two hours offset to match epoch and norwegian winter time.

    'datestart' => 1,
    'datelength' => 15,
    'offsetspan' => 21,

    // Dimensions on graph from Google Charts in pixels...
    'dimension.x' => 800,
    'dimension.y' => 350,

    /*
     * Do you want to generate statistics using the cron module? If so, specify which cron tag to use.
     * Examples: daily, weekly
     * To not run statistics in cron, set value to
     *     'cron_tag' => null,
     */
    'cron_tag' => 'daily',

    /*
     * Set max running time for this script. This is also controlled by max_execution_time in php.ini
     * and is set to 30 sec by default. Your web server can have other timeout configurations that may
     * also interrupt PHP execution. Apache has a Timeout directive and IIS has a
     * CGI timeout function. Both default to 300 seconds.
     */
    'time_limit' => 300,

    'timeres' => [
        'day' => [
            'name' => 'Day',
            'slot'              => 60 * 15, // Slots of 15 minutes
            'fileslot'          => 60 * 60 * 24, // One day (24 hours) file slots
            'axislabelint'      => 6 * 4, // Number of slots per label. 4 per hour *6 = 6 hours
            'dateformat-period' => 'j. M', //  4. Mars
            'dateformat-intra'  => 'j. M H:i', //  4. Mars 12:30
        ],
        'week' => [
            'name' => 'Week',
            'slot'              => 60 * 60, // Slots of one hour
            'fileslot'          => 60 * 60 * 24 * 7, // 7 days of data in each file
            'axislabelint'      => 24, // Number of slots per label. 24 is one each day
            'dateformat-period' => 'j. M', //  4. Mars
            'dateformat-intra'  => 'j. M H:i', //  4. Mars 12:30
        ],
        'month' => [
            'name' => 'Month',
            'slot'              => 60 * 60 * 24, // Slots of one day
            'fileslot'          => 60 * 60 * 24 * 30, // 30 days of data in each file
            'axislabelint'      => 7, // Number of slots per label. 7 days => 1 week
            'dateformat-period' => 'j. M Y H:i', //  4. Mars 12:30
            'dateformat-intra'  => 'j. M', //  4. Mars
        ],
        'monthaligned' => [
            'name'              => 'AlignedMonth',
            'slot'              => 60 * 60 * 24, // Slots of one day
            'fileslot'          => null, // 30 days of data in each file
            'customDateHandler' => 'month',
            'axislabelint'      => 7, // Number of slots per label. 7 days => 1 week
            'dateformat-period' => 'j. M Y H:i', //  4. Mars 12:30
            'dateformat-intra'  => 'j. M', //  4. Mars
        ],
        'days180' => [
            'name'              => '180 days',
            'slot'              => 60 * 60 * 24, // Slots of 1 day (24 hours)
            'fileslot'          => 60 * 60 * 24 * 180, // 80 days of data in each file
            'axislabelint'      => 30, // Number of slots per label. 7 days => 1 week
            'dateformat-period' => 'j. M', //  4. Mars
            'dateformat-intra'  => 'j. M', //  4. Mars
        ],
    ],

    'statrules' => [
        'sloratio' => [
            'name'         => 'SLO to SSO ratio',
            'descr'        => 'Comparison of the number of Single Log-Out compared to Single Sign-On.' .
                ' Graph shows how many logouts where initiated for each Single Sign-On.',
            'type'         => 'calculated',
            'presenter'    => 'statistics:Ratio',
            'ref'          => ['slo', 'sso'],
            'fieldPresentation' => [
                'class'    => 'statistics:Entity',
                'config'   => 'saml20-sp-remote',
            ],
        ],
        'ssomulti' => [
            'name'         => 'Requests per session',
            'descr'        => 'Number of SSO request pairs exchanged between IdP and SP within the same IdP session.' .
                ' A high number indicates that the session at the SP is timing out faster than at the IdP.',
            'type'         => 'calculated',
            'presenter'    => 'statistics:Ratio',
            'ref'          => ['sso', 'ssofirst'],
            'fieldPresentation' => [
                'class'    => 'statistics:Entity',
                'config'   => 'saml20-sp-remote',
            ],
        ],
        'sso' => [
            'name'         => 'SSO to service',
            'descr'        => 'The number of logins at a Service Provider.',
            'action'       => 'saml20-idp-SSO',
            'col'          => 6, // Service Provider EntityID
            'fieldPresentation' => [
                'class'    => 'statistics:Entity',
                'config'   => 'saml20-sp-remote',
            ],
        ],
        'ssofirst' => [
            'name'         => 'SSO-first to service',
            'descr'        => 'The number of logins at a Service Provider.',
            'action'       => 'saml20-idp-SSO-first',
            'col'          => 6, // Service Provider EntityID
            'fieldPresentation' => [
                'class'    => 'statistics:Entity',
                'config'   => 'saml20-sp-remote',
            ],
        ],
        'slo' => [
            'name'         => 'SLO initiated from service',
            'descr'        => 'The number of initated Sinlge Logout from each of the service providers.',
            'action'       => 'saml20-idp-SLO',
            'col'          => 7, // Service Provider EntityID that initiated the logout.
            'fieldPresentation' => [
                'class'    => 'statistics:Entity',
                'config'   => 'saml20-sp-remote',
            ],
        ],
        'consent' => [
            'name'         => 'Consent',
            'descr'        => 'Consent statistics. Everytime a user logs in to a service an entry is logged for' .
                ' one of three states: consent was found, consent was not found or consent storage was not available.',
            'action'       => 'consent',
            'col'          => 6,
            'fieldPresentation' => [
                'class'    => 'statistics:Entity',
                'config'   => 'saml20-sp-remote',
            ],
        ],
        'consentresponse' => [
            'name'         => 'Consent response',
            'descr'        => 'Consent response statistics. Everytime a user accepts consent,' .
                ' it is logged whether the user selected to remember the consent to next time.',
            'action'       => 'consentResponse',
            'col'          => 6,
            'fieldPresentation' => [
                'class'    => 'statistics:Entity',
                'config'   => 'saml20-sp-remote',
            ],
        ],
        'slopages' => [
            'name'         => 'SLO iframe pages',
            'descr'        => 'The varioust IFrame SLO pages a user visits',
            'action'       => 'slo-iframe',
            'col'          => 6, // Page the user visits.
        ],
        'slofail' => [
            'name'         => 'Failed iframe IdP-init SLOs',
            'descr'        => 'The number of logout failures from various SPs',
            'action'       => 'slo-iframe-fail',
            'col'          => 6, // Service Provider EntityID that wasn't logged out.
            'fieldPresentation' => [
                'class'    => 'statistics:Entity',
                'config'   => 'saml20-sp-remote',
            ],
        ],
    ],
];
