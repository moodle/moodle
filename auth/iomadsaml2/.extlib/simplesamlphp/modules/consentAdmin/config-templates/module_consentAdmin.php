<?php
/**
 * Config file for consentAdmin
 *
 * @author Jacob Christiansen, <jach@wayf.dk>
 * @package SimpleSAMLphp
 */
$config = [
    /*
     * Configuration for the database connection.
     */
    'consentadmin'  => [
        'consent:Database',
        'dsn'       =>  'mysql:host=DBHOST;dbname=DBNAME',
        'username'  =>  'USERNAME',
        'password'  =>  'PASSWORD',
    ],

    // Hash attributes including values or not
    'attributes.hash' => true,

    // If you set attributes.exclude in the consent module, this must match
    // 'attributes.exclude' => [],

    // Where to direct the user after logout
    // REMEMBER to prefix with http:// otherwise the relaystate is only appended
    // to saml2 logout URL
    'returnURL' => 'http://www.wayf.dk',

    // Shows description of the services if set to true (defaults to true)
    'showDescription' => true,

    // Set authority
    'authority' => 'saml2',
];
