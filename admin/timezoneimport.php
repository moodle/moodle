<?php

    // Automatic update of Timezones from a new source

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->libdir.'/olson.php');

    admin_externalpage_setup('timezoneimport');

    $ok = optional_param('ok', 0, PARAM_BOOL);


/// Print headings

    $strimporttimezones = get_string('importtimezones', 'admin');

    echo $OUTPUT->header();

    echo $OUTPUT->heading($strimporttimezones);

    if (!$ok or !confirm_sesskey()) {
        $message = '<br /><br />';
        $message .= $CFG->dataroot.'/temp/olson.txt<br />';
        $message .= $CFG->dataroot.'/temp/timezone.txt<br />';
        $message .= '<a href="http://download.moodle.org/timezone/">http://download.moodle.org/timezone/</a><br />';
        $message .= '<a href="'.$CFG->wwwroot.'/lib/timezone.txt">'.$CFG->dirroot.'/lib/timezone.txt</a><br />';
        $message .= '<br />';

        $message = get_string("configintrotimezones", 'admin', $message);

        echo $OUTPUT->confirm($message, 'timezoneimport.php?ok=1', 'index.php');

        echo $OUTPUT->footer();
        exit;
    }


/// Try to find a source of timezones to import from

    $importdone = false;

/// First, look for an Olson file locally

    $source = $CFG->dataroot.'/temp/olson.txt';
    if (!$importdone and is_readable($source)) {
        if ($timezones = olson_to_timezones($source)) {
            update_timezone_records($timezones);
            $importdone = $source;
        }
    }

/// Next, look for a CSV file locally

    $source = $CFG->dataroot.'/temp/timezone.txt';
    if (!$importdone and is_readable($source)) {
        if ($timezones = get_records_csv($source, 'timezone')) {
            update_timezone_records($timezones);
            $importdone = $source;
        }
    }

/// Otherwise, let's try moodle.org's copy
    $source = 'http://download.moodle.org/timezone/';
    if (!$importdone && ($content=download_file_content($source))) {
        if ($file = fopen($CFG->dataroot.'/temp/timezone.txt', 'w')) {            // Make local copy
            fwrite($file, $content);
            fclose($file);
            if ($timezones = get_records_csv($CFG->dataroot.'/temp/timezone.txt', 'timezone')) {  // Parse it
                update_timezone_records($timezones);
                $importdone = $source;
            }
            unlink($CFG->dataroot.'/temp/timezone.txt');
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
        echo $OUTPUT->heading(get_string('importtimezonescount', 'admin', $a), 3);

        echo $OUTPUT->continue_button('index.php');

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
        echo $OUTPUT->heading(get_string('importtimezonesfailed', 'admin'), 3);
        echo $OUTPUT->continue_button('index.php');
    }

    echo $OUTPUT->footer();


