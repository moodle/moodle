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
        if(isset($form->mode_dst)) {
            // Move to DST presets configuration
            redirect('dst.php');
            die();
        }
        // Normal configuration, just save the variables
        if(isset($form->adminseesallcourses)) {
            set_config('calendar_adminseesall', intval($form->adminseesallcourses) != 0);
            unset($SESSION->cal_courses_shown);
        }
        if(isset($form->dstforusers)) {
            if($form->dstforusers == 'force') {
                $preset = optional_param('dstpreset', 0, PARAM_INT);
            }
            else {
                $preset = 0;
            }
            set_config('calendar_dstforusers', $preset);
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
    }

    // Include the calendar library AFTER modifying the data, so we read the latest values
    require_once('../calendar/lib.php');

    // Populate some variables we 're going to need in calendar.html

    $presets = get_records('dst_preset');
    if(!empty($presets)) {
        foreach($presets as $id => $preset) {
            $presets[$id] = $preset->name;
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

    print_simple_box_start('center');
    include('./calendar.html');
    print_simple_box_end();

    print_footer();

?>
