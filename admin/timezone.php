<?php   // $Id$

    require_once('../config.php');

    $zone = optional_param('zone', '', PARAM_PATH); //not a path, but it looks like it anyway

    require_login();

    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

    $strtimezone = get_string("timezone");
    $strsavechanges = get_string("savechanges");
    $strusers = get_string("users");
    $strall = get_string("all");

    print_header($strtimezone, $strtimezone, build_navigation(array(array('name' => $strtimezone, 'link' => null, 'type' => 'misc'))));

    print_heading("");

    if (!empty($zone) and confirm_sesskey()) {
        $db->debug = true;
        echo "<center>";
        execute_sql("UPDATE {$CFG->prefix}user SET timezone = '$zone'");
        $db->debug = false;
        echo "</center>";

        $USER->timezone = $zone;
    }

    require_once($CFG->dirroot.'/calendar/lib.php');
    $timezones = get_list_of_timezones();

    echo '<center><form action="timezone.php" method="get">';
    echo "$strusers ($strall): ";
    choose_from_menu ($timezones, "zone", 99, get_string("serverlocaltime"), "", "99");
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
    echo "<input type=\"submit\" value=\"$strsavechanges\" />";
    echo "</form></center>";

    print_footer();

?>
