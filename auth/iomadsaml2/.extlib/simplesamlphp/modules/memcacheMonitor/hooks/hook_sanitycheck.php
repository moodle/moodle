<?php

/**
 * Sanity check for memcache servers.
 *
 * This function verifies that all memcache servers work.
 *
 * @param array &$hookinfo  hookinfo
 * @return void
 */
function memcacheMonitor_hook_sanitycheck(&$hookinfo)
{
    assert(is_array($hookinfo));
    assert(array_key_exists('errors', $hookinfo));
    assert(array_key_exists('info', $hookinfo));

    try {
        $servers = \SimpleSAML\Memcache::getRawStats();
    } catch (\Exception $e) {
        $hookinfo['errors'][] = '[memcacheMonitor] Error parsing memcache configuration: '.$e->getMessage();
        return;
    }

    $allOK = true;
    foreach ($servers as $group) {
        foreach ($group as $server => $status) {
            if ($status === false) {
                $hookinfo['errors'][] = '[memcacheMonitor] No response from server: '.$server;
                $allOK = false;
            }
        }
    }

    if ($allOK) {
        $hookinfo['info'][] = '[memcacheMonitor] All servers responding.';
    }
}
