<?PHP // $Id$

    // Allows the admin to configure calendar and date/time stuff

    require_once('../config.php');

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

    print_header("$site->shortname: $strcalendarsettings", "$site->fullname",
                 "<a href=\"index.php\">$stradministration</a> -> ".
                 "<a href=\"configure.php\">$strconfiguration</a> -> $strcalendarsettings");

    print_heading($strcalendarsettings);

/// If data submitted, process and store

    if(($form = data_submitted()) && confirm_sesskey()) {
        if(isset($form->mode_timezone_update)) {
            redirect('dst_update.php');
            die();
        }
        // Normal configuration, just save the variables
        if(isset($form->adminseesallcourses)) {
            set_config('calendar_adminseesall', intval($form->adminseesallcourses) != 0);
            unset($SESSION->cal_courses_shown);
        }
        if(isset($form->forcetimezone)) {
            if($form->forcetimezone == 'force') {
                $preset = optional_param('timezonepreset', '');
                // Some replaces to prevent SQL injection
                $preset = str_replace(';', '', $preset);
                $preset = str_replace('\'', '', $preset);
            }
            else {
                $preset = '';
            }
            set_config('forcetimezone', $preset);
        }
        if(isset($form->startwday)) {
            $startwday = intval($form->startwday);
            if($startwday >= 0 && $startwday <= 6) {
                set_config('calendar_startwday', $startwday);
            }
        }
        if(isset($form->weekend)) {
            if(is_array($form->weekend)) {
                // Creating a packed bitfield; look at /calendar/lib.php if you can't figure it out
                $bitfield = 0;
                foreach($form->weekend as $day) {
                    $bitfield |= (1 << (intval($day) % 7));
                }
                if($bitfield > 0) {
                    set_config('calendar_weekend', $bitfield);
                }
            }
        }
        if(isset($form->lookahead)) {
            $lookahead = intval($form->lookahead);
            if($lookahead > 0) {
                set_config('calendar_lookahead', $lookahead);
            }
        }
        if(isset($form->maxevents)) {
            $maxevents = intval($form->maxevents);
            if($maxevents > 0) {
                set_config('calendar_maxevents', $maxevents);
            }
        }
        redirect('index.php');
    }

    // Include the calendar library AFTER modifying the data, so we read the latest values
    require_once('../calendar/lib.php');

    // Populate some variables we 're going to need in calendar.html
    $presets = get_records_sql('SELECT id, name FROM '.$CFG->prefix.'timezone GROUP BY name');

    if(!empty($presets)) {
        foreach($presets as $id => $preset) {
            $presets[$preset->name] = get_string($preset->name, 'timezones');
            unset($presets[$id]);
        }
    }

    asort($presets); // Sort before adding trivial presets because string sorts mess up their order

    for($i = -13; $i <= 13; $i += .5) {
        $tzstring = get_string('unspecifiedlocation', 'timezones').' / GMT';
        if($i < 0) {
            $presets["$i"] = $tzstring . $i;
        }
        else if($i > 0) {
            $presets["$i"] = $tzstring . '+' . $i;
        }
        else {
            $presets["$i"] = $tzstring;
        }
    }

    $weekdays = array(
        0 => get_string('sunday', 'calendar'),
        1 => get_string('monday', 'calendar'),
        2 => get_string('tuesday', 'calendar'),
        3 => get_string('wednesday', 'calendar'),
        4 => get_string('thursday', 'calendar'),
        5 => get_string('friday', 'calendar'),
        6 => get_string('saturday', 'calendar')
    );

    // Main display starts here

    include('./calendar.html');

    print_footer();

?>
