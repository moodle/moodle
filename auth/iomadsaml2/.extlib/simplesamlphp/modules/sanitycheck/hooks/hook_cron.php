<?php

/**
 * Hook to run a cron job.
 *
 * @param array &$croninfo  Output
 * @return void
 */
function sanitycheck_hook_cron(&$croninfo)
{
    assert(is_array($croninfo));
    assert(array_key_exists('summary', $croninfo));
    assert(array_key_exists('tag', $croninfo));

    \SimpleSAML\Logger::info('cron [sanitycheck]: Running cron in cron tag ['.$croninfo['tag'].'] ');

    try {
        $sconfig = \SimpleSAML\Configuration::getOptionalConfig('config-sanitycheck.php');

        $cronTag = $sconfig->getString('cron_tag', null);
        if ($cronTag === null || $cronTag !== $croninfo['tag']) {
            return;
        }

        $info = [];
        $errors = [];
        $hookinfo = [
            'info' => &$info,
            'errors' => &$errors,
        ];

        SimpleSAML\Module::callHooks('sanitycheck', $hookinfo);

        if (count($errors) > 0) {
            foreach ($errors as $err) {
                $croninfo['summary'][] = 'Sanitycheck error: '.$err;
            }
        }
    } catch (\Exception $e) {
        $croninfo['summary'][] = 'Error executing sanity check: '.$e->getMessage();
    }
}
