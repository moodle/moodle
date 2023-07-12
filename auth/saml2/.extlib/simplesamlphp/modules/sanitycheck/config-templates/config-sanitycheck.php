<?php
/*
 * The configuration of SimpleSAMLphp sanitycheck package
 */

$config = [
    /*
     * Do you want to generate statistics using the cron module? If so, specify which cron tag to use.
     * Examples: daily, weekly
     * To not run statistics in cron, set value to
     *     'cron_tag' => null,
     */
    'cron_tag' => 'hourly',
];
