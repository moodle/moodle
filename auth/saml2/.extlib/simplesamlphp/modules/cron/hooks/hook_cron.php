<?php

/**
 * Hook to run a cron job.
 *
 * @param array &$croninfo  Output
 * @return void
 */
function cron_hook_cron(&$croninfo)
{
    assert(is_array($croninfo));
    assert(array_key_exists('summary', $croninfo));
    assert(array_key_exists('tag', $croninfo));

    $cronconfig = \SimpleSAML\Configuration::getConfig('module_cron.php');

    if ($cronconfig->getValue('debug_message', true)) {
        $croninfo['summary'][] = 'Cron did run tag [' . $croninfo['tag'] . '] at ' . date(DATE_RFC822);
    }
}
