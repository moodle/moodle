<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Store performance test run + output script.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/lib/adminlib.php');
require_once($CFG->dirroot.'/cache/locallib.php');

$count = optional_param('count', 100, PARAM_INT);
$count = min($count, 100000);
$count = max($count, 0);

admin_externalpage_setup('cachetestperformance');

$applicationtable = new html_table();
$applicationtable->head = array(
    get_string('plugin', 'cache'),
    get_string('result', 'cache'),
    get_string('set', 'cache'),
    get_string('gethit', 'cache'),
    get_string('getmiss', 'cache'),
    get_string('delete', 'cache'),
);
$applicationtable->data = array();
$sessiontable = clone($applicationtable);
$requesttable = clone($applicationtable);


$application = cache_definition::load_adhoc(cache_store::MODE_APPLICATION, 'cache', 'applicationtest');
$session = cache_definition::load_adhoc(cache_store::MODE_SESSION, 'cache', 'sessiontest');
$request = cache_definition::load_adhoc(cache_store::MODE_REQUEST, 'cache', 'requesttest');

$strinvalidplugin = new lang_string('invalidplugin', 'cache');
$strunsupportedmode = new lang_string('unsupportedmode', 'cache');
$struntestable = new lang_string('untestable', 'cache');
$strtested = new lang_string('tested', 'cache');
$strnotready = new lang_string('storenotready', 'cache');

foreach (core_component::get_plugin_list_with_file('cachestore', 'lib.php', true) as $plugin => $path) {

    $class = 'cachestore_'.$plugin;
    $plugin = get_string('pluginname', 'cachestore_'.$plugin);

    if (!class_exists($class) || !method_exists($class, 'initialise_test_instance') || !$class::are_requirements_met()) {
        $applicationtable->data[] = array($plugin, $strinvalidplugin, '-', '-', '-', '-');
        $sessiontable->data[] = array($plugin, $strinvalidplugin, '-', '-', '-', '-');
        $requesttable->data[] = array($plugin, $strinvalidplugin, '-', '-', '-', '-');
        continue;
    }

    if (!$class::is_supported_mode(cache_store::MODE_APPLICATION)) {
        $applicationtable->data[] = array($plugin, $strunsupportedmode, '-', '-', '-', '-');
    } else {
        $store = $class::initialise_test_instance($application);
        if ($store === false) {
            $applicationtable->data[] = array($plugin, $struntestable, '-', '-', '-', '-');
        } else if (!$store->is_ready()) {
            $applicationtable->data[] = array($plugin, $strnotready, '-', '-', '-', '-');
        } else {
            $result = array($plugin, $strtested, 0, 0, 0);
            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->set('key'.$i, 'test data '.$i);
            }
            $result[2] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->get('key'.$i);
            }
            $result[3] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->get('fake'.$i);
            }
            $result[4] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->delete('key'.$i);
            }
            $result[5] = sprintf('%01.4f', microtime(true) - $start);
            $applicationtable->data[] = $result;
            $store->instance_deleted();
        }
    }

    if (!$class::is_supported_mode(cache_store::MODE_SESSION)) {
        $sessiontable->data[] = array($plugin, $strunsupportedmode, '-', '-', '-', '-');
    } else {
        $store = $class::initialise_test_instance($session);
        if ($store === false) {
            $sessiontable->data[] = array($plugin, $struntestable, '-', '-', '-', '-');
        } else if (!$store->is_ready()) {
            $sessiontable->data[] = array($plugin, $strnotready, '-', '-', '-', '-');
        } else {
            $result = array($plugin, $strtested, 0, 0, 0);
            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->set('key'.$i, 'test data '.$i);
            }
            $result[2] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->get('key'.$i);
            }
            $result[3] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->get('fake'.$i);
            }
            $result[4] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->delete('key'.$i);
            }
            $result[5] = sprintf('%01.4f', microtime(true) - $start);
            $sessiontable->data[] = $result;
            $store->instance_deleted();
        }
    }

    if (!$class::is_supported_mode(cache_store::MODE_REQUEST)) {
        $requesttable->data[] = array($plugin, $strunsupportedmode, '-', '-', '-', '-');
    } else {
        $store = $class::initialise_test_instance($request);
        if ($store === false) {
            $requesttable->data[] = array($plugin, $struntestable, '-', '-', '-', '-');
        } else if (!$store->is_ready()) {
            $requesttable->data[] = array($plugin, $strnotready, '-', '-', '-', '-');
        } else {
            $result = array($plugin, $strtested, 0, 0, 0);
            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->set('key'.$i, 'test data '.$i);
            }
            $result[2] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->get('key'.$i);
            }
            $result[3] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->get('fake'.$i);
            }
            $result[4] = sprintf('%01.4f', microtime(true) - $start);

            $start = microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $store->delete('key'.$i);
            }
            $result[5] = sprintf('%01.4f', microtime(true) - $start);
            $requesttable->data[] = $result;
            $store->instance_deleted();
        }
    }

}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('storeperformance', 'cache', $count));

$possiblecounts = array(1, 10, 100, 500, 1000, 5000, 10000, 50000, 100000);
$links = array();
foreach ($possiblecounts as $pcount) {
    $links[] = html_writer::link(new moodle_url($PAGE->url, array('count' => $pcount)), $pcount);
}
echo $OUTPUT->box_start('generalbox performance-test-counts');
echo get_string('requestcount', 'cache', join(', ', $links));
echo $OUTPUT->box_end();

echo $OUTPUT->heading(get_string('storeresults_application', 'cache'));
echo html_writer::table($applicationtable);

echo $OUTPUT->heading(get_string('storeresults_session', 'cache'));
echo html_writer::table($sessiontable);

echo $OUTPUT->heading(get_string('storeresults_request', 'cache'));
echo html_writer::table($requesttable);

echo $OUTPUT->footer();
