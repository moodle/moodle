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

    if(confirm_sesskey() && $form = data_submitted()) {
        if(isset($form->mode_dst)) {
            // Move to DST presets configuration
            redirect('dst.php?sesskey='.$USER->sesskey);
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
    }

    $presets = get_records('dst_preset');
    if(!empty($presets)) {
        foreach($presets as $id => $preset) {
            $presets[$id] = $preset->name;
        }
    }

/// Main display starts here

    print_simple_box_start('center', '100%', $THEME->cellheading);
    include('./calendar.html');
    print_simple_box_end();

    print_footer();

?>
