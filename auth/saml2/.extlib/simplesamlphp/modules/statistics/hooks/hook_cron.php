<?php

/**
 * Hook to run a cron job.
 *
 * @param array &$croninfo  Output
 * @return void
 */
function statistics_hook_cron(&$croninfo)
{
    assert(is_array($croninfo));
    assert(array_key_exists('summary', $croninfo));
    assert(array_key_exists('tag', $croninfo));

    $statconfig = \SimpleSAML\Configuration::getConfig('module_statistics.php');

    if (is_null($statconfig->getValue('cron_tag', null))) {
        return;
    }
    if ($statconfig->getValue('cron_tag', null) !== $croninfo['tag']) {
        return;
    }

    $maxtime = $statconfig->getInteger('time_limit', null);
    if ($maxtime) {
        set_time_limit($maxtime);
    }

    try {
        $aggregator = new \SimpleSAML\Module\statistics\Aggregator();
        $results = $aggregator->aggregate();
        if (empty($results)) {
            \SimpleSAML\Logger::notice('Output from statistics aggregator was empty.');
        } else {
            $aggregator->store($results);
        }
    } catch (\Exception $e) {
        $message = 'Loganalyzer threw exception: ' . $e->getMessage();
        \SimpleSAML\Logger::warning($message);
        $croninfo['summary'][] = $message;
    }
}
