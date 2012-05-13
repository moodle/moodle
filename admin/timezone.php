<?php

    require_once('../config.php');

    $zone = optional_param('zone', '', PARAM_RAW);

    if (!is_numeric($zone)) {
         //not a path, but it looks like it anyway
         $zone = clean_param($zone, PARAM_PATH);
    }

    $PAGE->set_url('/admin/timezone.php');
    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    $strtimezone = get_string("timezone");
    $strsavechanges = get_string("savechanges");
    $strusers = get_string("users");
    $strall = get_string("all");

    $PAGE->set_title($strtimezone);
    $PAGE->set_heading($strtimezone);
    $PAGE->navbar->add($strtimezone);
    echo $OUTPUT->header();

    echo $OUTPUT->heading("");

    if (data_submitted() and !empty($zone) and confirm_sesskey()) {
        echo "<center>";
        $DB->execute("UPDATE {user} SET timezone = ?", array($zone));
        echo "</center>";

        $USER->timezone = $zone;
        $current = $zone;
        echo $OUTPUT->notification('Timezone of all users changed', 'notifysuccess');
    } else {
        $current = 99;
    }

    require_once($CFG->dirroot.'/calendar/lib.php');
    $timezones = get_list_of_timezones();

    echo '<center><form action="timezone.php" method="post">';
    echo "$strusers ($strall): ";
    echo html_writer::select($timezones, "zone", $current, array('99'=>get_string("serverlocaltime")));
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />";
    echo '<input type="submit" value="'.s($strsavechanges).'" />';
    echo "</form></center>";

    echo $OUTPUT->footer();


