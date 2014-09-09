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
 * Automatic update of Timezones from a new source
 *
 * @package    tool
 * @subpackage timezoneimport
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once('../../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->libdir.'/olson.php');

    admin_externalpage_setup('tooltimezoneimport');

    $ok = optional_param('ok', 0, PARAM_BOOL);


/// Print headings

    $strimporttimezones = get_string('importtimezones', 'tool_timezoneimport');

    echo $OUTPUT->header();

    echo $OUTPUT->heading($strimporttimezones);

    if (!$ok or !confirm_sesskey()) {
        $message = '<br /><br />';
        $message .= $CFG->tempdir.'/olson.txt<br />';
        $message .= $CFG->tempdir.'/timezone.txt<br />';
        $message .= '<a href="https://download.moodle.org/timezone/">https://download.moodle.org/timezone/</a><br />';
        $message .= '<a href="'.$CFG->wwwroot.'/lib/timezone.txt">'.$CFG->dirroot.'/lib/timezone.txt</a><br />';
        $message .= '<br />';

        $message = get_string("configintrotimezones", 'tool_timezoneimport', $message);

        echo $OUTPUT->confirm($message, 'index.php?ok=1', new moodle_url('/admin/index.php'));

        echo $OUTPUT->footer();
        exit;
    }


/// Try to find a source of timezones to import from

    $importdone = false;

/// First, look for an Olson file locally

    $source = $CFG->tempdir.'/olson.txt';
    if (!$importdone and is_readable($source)) {
        if ($timezones = olson_to_timezones($source)) {
            update_timezone_records($timezones);
            $importdone = $source;
        }
    }

/// Next, look for a CSV file locally

    $source = $CFG->tempdir.'/timezone.txt';
    if (!$importdone and is_readable($source)) {
        if ($timezones = get_records_csv($source, 'timezone')) {
            update_timezone_records($timezones);
            $importdone = $source;
        }
    }

/// Otherwise, let's try moodle.org's copy
    $source = 'https://download.moodle.org/timezone/';
    if (!$importdone && ($content=download_file_content($source))) {
        if ($file = fopen($CFG->tempdir.'/timezone.txt', 'w')) {            // Make local copy
            fwrite($file, $content);
            fclose($file);
            if ($timezones = get_records_csv($CFG->tempdir.'/timezone.txt', 'timezone')) {  // Parse it
                update_timezone_records($timezones);
                $importdone = $source;
            }
            unlink($CFG->tempdir.'/timezone.txt');
        }
    }


/// Final resort, use the copy included in Moodle
    $source = $CFG->dirroot.'/lib/timezone.txt';
    if (!$importdone and is_readable($source)) {  // Distribution file
        if ($timezones = get_records_csv($source, 'timezone')) {
            update_timezone_records($timezones);
            $importdone = $source;
        }
    }


/// That's it!

    if ($importdone) {
        $a = new stdClass();
        $a->count = count($timezones);
        $a->source  = $importdone;
        echo $OUTPUT->heading(get_string('importtimezonescount', 'tool_timezoneimport', $a), 3);

        echo $OUTPUT->continue_button(new moodle_url('/admin/index.php'));

        $timezonelist = array();
        foreach ($timezones as $timezone) {
            if (is_array($timezone)) {
                $timezone = (object)$timezone;
            }
            if (isset($timezonelist[$timezone->name])) {
                $timezonelist[$timezone->name]++;
            } else {
                $timezonelist[$timezone->name] = 1;
            }
        }
        ksort($timezonelist);

        echo "<br />";
        echo $OUTPUT->box_start();
        foreach ($timezonelist as $name => $count) {
            echo "$name ($count)<br />";
        }
        echo $OUTPUT->box_end();

    } else {
        echo $OUTPUT->heading(get_string('importtimezonesfailed', 'tool_timezoneimport'), 3);
        echo $OUTPUT->continue_button(new moodle_url('/admin/index.php'));
    }

    echo $OUTPUT->footer();


