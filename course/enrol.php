<?PHP // $Id$
      // Depending on the current enrolment method, this page 
      // presents the user with whatever they need to know when 
      // they try to enrol in a course.

    require_once("../config.php");
    require_once("lib.php");
    require_once("$CFG->dirroot/enrol/$CFG->enrol/enrol.php");

    require_variable($id);

    require_login();

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (! $site = get_site()) {
        error("Could not find a site!");
    }

    check_for_restricted_user($USER->username);

    $enrol = new enrolment_plugin();

/// Check the submitted enrollment key if there is one

    if ($form = data_submitted()) {
        $enrol->check_entry($form, $course);
    }

    $enrol->print_entry($course);

/// Easy!

?>
