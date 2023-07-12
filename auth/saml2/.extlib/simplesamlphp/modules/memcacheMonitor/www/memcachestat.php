<?php

/**
 * @param int $input
 * @return string
 */
function tdate($input)
{
    return date(DATE_RFC822, $input);
}


/**
 * @param int $input
 * @return string
 */
function hours($input)
{
    if ($input < 60) {
        return number_format($input, 2).' sec';
    }
    if ($input < 60 * 60) {
        return number_format(($input / 60), 2).' min';
    }
    if ($input < 24 * 60 * 60) {
        return number_format(($input / (60 * 60)), 2).' hours';
    }
    return number_format($input / (24 * 60 * 60), 2).' days';
}


/**
 * @param int $input
 * @return string
 */
function humanreadable($input)
{
    $output = "";
    $input = abs($input);

    if ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 100)) {
        $output = sprintf("%5ldEi", $input / (1024 * 1024 * 1024 * 1024 * 1024 * 1024));
    } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 1024 * 10)) {
        $output = sprintf("%5.1fEi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 1024)) {
        $output = sprintf("%5.2fEi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 100)) {
        $output = sprintf("%5ldPi", $input / (1024 * 1024 * 1024 * 1024 * 1024));
    } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024 * 10)) {
        $output = sprintf("%5.1fPi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 1024)) {
        $output = sprintf("%5.2fPi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 100)) {
        $output = sprintf("%5ldTi", $input / (1024 * 1024 * 1024 * 1024));
    } elseif ($input >= (1024 * 1024 * 1024 * 1024 * 10)) {
        $output = sprintf("%5.1fTi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024 * 1024 * 1024)) {
        $output = sprintf("%5.2fTi", $input / (1024.0 * 1024.0 * 1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024 * 1024 * 100)) {
        $output = sprintf("%5ldGi", $input / (1024 * 1024 * 1024));
    } elseif ($input >= (1024 * 1024 * 1024 * 10)) {
        $output = sprintf("%5.1fGi", $input / (1024.0 * 1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024 * 1024)) {
        $output = sprintf("%5.2fGi", $input / (1024.0 * 1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024 * 100)) {
        $output = sprintf("%5ldMi", $input / (1024 * 1024));
    } elseif ($input >= (1024 * 1024 * 10)) {
        $output = sprintf("%5.1fM", $input / (1024.0 * 1024.0));
    } elseif ($input >= (1024 * 1024)) {
        $output = sprintf("%5.2fMi", $input / (1024.0 * 1024.0));
    } elseif ($input >= (1024 * 100)) {
        $output = sprintf("%5ldKi", $input / 1024);
    } elseif ($input >= (1024 * 10)) {
        $output = sprintf("%5.1fKi", $input / 1024.0);
    } elseif ($input >= (1024)) {
        $output = sprintf("%5.2fKi", $input / 1024.0);
    } else {
        $output = sprintf("%5ld", $input);
    }

    return $output;
}

$config = \SimpleSAML\Configuration::getInstance();

// Make sure that the user has admin access rights
\SimpleSAML\Utils\Auth::requireAdmin();

$formats = [
    'bytes' => 'humanreadable',
    'bytes_read' => 'humanreadable',
    'bytes_written' => 'humanreadable',
    'limit_maxbytes' => 'humanreadable',
    'time' => 'tdate',
    'uptime' => 'hours',
];

$statsraw = \SimpleSAML\Memcache::getStats();

$stats = $statsraw;

foreach ($stats as $key => &$entry) {
    if (array_key_exists($key, $formats)) {
        $func = $formats[$key];
        foreach ($entry as $k => $val) {
            $entry[$k] = $func($val);
        }
    }
}

$t = new \SimpleSAML\XHTML\Template($config, 'memcacheMonitor:memcachestat.tpl.php');
$rowTitles = [
    'accepting_conns' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:accepting_conns}'),
    'auth_cmds' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:auth_cmds}'),
    'auth_errors' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:auth_errors}'),
    'bytes' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:bytes}'),
    'bytes_read' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:bytes_read}'),
    'bytes_written' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:bytes_written}'),
    'cas_badval' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:cas_badval}'),
    'cas_hits' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:cas_hits}'),
    'cas_misses' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:cas_misses}'),
    'cmd_flush' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:cmd_flush}'),
    'cmd_get' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:cmd_get}'),
    'cmd_set' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:cmd_set}'),
    'cmd_touch' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:cmd_touch}'),
    'connection_structures' => \SimpleSAML\Locale\Translate::noop(
        '{memcacheMonitor:memcachestat:connection_structures}'
    ),
    'conn_yields' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:conn_yields}'),
    'curr_connections' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:curr_connections}'),
    'curr_items' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:curr_items}'),
    'decr_hits' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:decr_hits}'),
    'decr_misses' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:decr_misses}'),
    'delete_hits' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:delete_hits}'),
    'delete_misses' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:delete_misses}'),
    'expired_unfetched' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:expired_unfetched}'),
    'evicted_unfetched' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:evicted_unfetched}'),
    'evictions' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:evictions}'),
    'get_hits' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:get_hits}'),
    'get_misses' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:get_misses}'),
    'hash_bytes' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:hash_bytes}'),
    'hash_is_expanding' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:hash_is_expanding}'),
    'hash_power_level' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:hash_power_level}'),
    'incr_hits' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:incr_hits}'),
    'incr_misses' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:incr_misses}'),
    'libevent' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:libevent}'),
    'limit_maxbytes' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:limit_maxbytes}'),
    'listen_disabled_num' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:listen_disabled_num}'),
    'pid' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:pid}'),
    'pointer_size' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:pointer_size}'),
    'reclaimed' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:reclaimed}'),
    'reserved_fds' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:reserved_fds}'),
    'rusage_system' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:rusage_system}'),
    'rusage_user' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:rusage_user}'),
    'threads' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:threads}'),
    'time' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:time}'),
    'total_connections' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:total_connections}'),
    'total_items' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:total_items}'),
    'touch_hits' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:touch_hits}'),
    'touch_misses' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:touch_misses}'),
    'uptime' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:uptime}'),
    'version' => \SimpleSAML\Locale\Translate::noop('{memcacheMonitor:memcachestat:version}'),
];

// Identify column headings
$colTitles = [];
foreach ($stats as $rowTitle => $rowData) {
    foreach ($rowData as $colTitle => $foo) {
        if (!in_array($colTitle, $colTitles, true)) {
            $colTitles[] = $colTitle;
        }
    }
}

if (array_key_exists('bytes', $statsraw) && array_key_exists('limit_maxbytes', $statsraw)) {
    $usage = [];
    $maxpix = 400;
    foreach ($statsraw['bytes'] as $key => $row_data) {
        $pix = floor($statsraw['bytes'][$key] * $maxpix / $statsraw['limit_maxbytes'][$key]);
        $usage[$key] = $pix.'px';
    }
    $t->data['maxpix'] = $maxpix.'px';
    $t->data['usage'] = $usage;
}

$t->data['title'] = 'Memcache stats';
$t->data['rowTitles'] = $rowTitles;
$t->data['colTitles'] = $colTitles;
$t->data['statsraw'] = $statsraw;
$t->data['table'] = $stats;
$t->show();
