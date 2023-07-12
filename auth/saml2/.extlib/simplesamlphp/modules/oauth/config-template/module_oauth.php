<?php
/*
 * Configuration for the OAuth module.
 *
 */

$config = [
    /* Enable the getUserInfo endpoint. Do not enable unless you know what you do.
     * It may give external parties access to userInfo unless properly secured.
     */
    'getUserInfo.enable' => true,

    'requestTokenDuration' => 60 * 30, // 30 minutes
    'accessTokenDuration'  => 60 * 60 * 24, // 24 hours
    'nonceCache'           => 60 * 60 * 24 * 14, // 14 days

    // Tag to run storage cleanup script using the cron module...
    'cron_tag' => 'hourly',

    // auth is the idp to use for admin authentication,
    // useridattr is the attribute-name that contains the userid as returned from idp
    'auth' => 'default-sp',
    'useridattr', 'user',
];
