<?php // $Id$

    // Automatic update of Timezones from a new source

    require_once('../config.php');
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->libdir.'/olson.php');

    $ok = optional_param('ok');

    require_login();

    if (!isadmin()) {
        error('Only administrators can use this page!');
    }

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }

/// Print headings

    $stradministration = get_string('administration');
    $strconfiguration = get_string('configuration');
    $strcalendarsettings = get_string('calendarsettings', 'admin');
    $strimporttimezones = get_string('importtimezones', 'admin');

    print_header("$site->shortname: $strcalendarsettings", "$site->fullname",
                 "<a href=\"index.php\">$stradministration</a> -> ".
                 "<a href=\"configure.php\">$strconfiguration</a> -> ".
                 "<a href=\"calendar.php\">$strcalendarsettings</a> -> $strimporttimezones");

    print_heading($strimporttimezones);

    if (!$ok or !confirm_sesskey()) {
        $message = '<p>';
        $message .= $CFG->dataroot.'/temp/olson.txt<br />';
        $message .= $CFG->dataroot.'/temp/timezones.txt<br />';
        $message .= '<a href="http://download.moodle.org/timezones/">http://download.moodle.org/timezones/</a><br />';
        $message .= '<a href="'.$CFG->wwwroot.'/lib/timezones.txt">'.$CFG->dirroot.'/lib/timezones.txt</a><br />';
        $message .= '</p>';

        $message = get_string("configintrotimezones", 'admin', $message);

        notice_yesno($message, 'timezoneimport.php?ok=1&sesskey='.sesskey(), 'calendar.php');

        print_footer();
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

    $source = $CFG->dataroot.'/temp/timezones.txt';
    if (!$importdone and is_readable($source)) {
        if ($timezones = get_records_csv($source, 'timezone')) {
            update_timezone_records($timezones);
            $importdone = $source;
        }
    }

/// Otherwise, let's try moodle.org's copy

    $source = 'http://download.moodle.org/timezones/';
    if (!$importdone and ini_get('allow_url_fopen')) {
        if ($contents = file_get_contents($source)) {  // Grab whole page
            if ($file = fopen($CFG->dataroot.'/temp/timezones.txt', 'w')) {            // Make local copy
                fwrite($file, $contents);
                fclose($file);
                if ($timezones = get_records_csv($CFG->dataroot.'/temp/timezones.txt', 'timezone')) {  // Parse it
                    update_timezone_records($timezones);
                    $importdone = $source;
                }
                unlink($CFG->dataroot.'/temp/timezones.txt');
            }
        }
    }


/// Final resort, use the copy included in Moodle

    $source = $CFG->dirroot.'/lib/timezones.txt';
    if (!$importdone and is_readable($source)) {  // Distribution file
        if ($timezones = get_records_csv($source)) {
            update_timezone_records($timezones);
            $importdone = $source;
        }
    }


/// That's it!

    if ($importdone) {
        $a = null;
        $a->count = count($timezones);
        $a->source  = $importdone;
        print_heading(get_string('importtimezonescount', 'admin', $a), '', 3);

        print_continue('calendar.php');

        $timezonelist = array();
        foreach ($timezones as $timezone) {
            if (isset($timezonelist[$timezone->name])) {
                $timezonelist[$timezone->name]++;
            } else {
                $timezonelist[$timezone->name] = 1;
            }
        }
        ksort($timezonelist);

        echo "<br />";
        print_simple_box_start('center');
        foreach ($timezonelist as $name => $count) {
            echo "$name ($count)<br />";
        }
        print_simple_box_end();

    } else {
        print_heading(get_string('importtimezonesfailed', 'admin'), '', 3);
        print_continue('calendar.php');
    }

    print_footer();

?>
