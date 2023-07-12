<?php

/*
 * Configuration for the Cron module.
 */

$config = [
    'key' => 'secret',
    'allowed_tags' => ['daily', 'hourly', 'frequent'],
    'debug_message' => true,
    'sendemail' => true,
];
