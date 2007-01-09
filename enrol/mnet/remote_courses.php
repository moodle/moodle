<?PHP  // $Id$
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once(dirname(__FILE__) . "/../../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    $adminroot = admin_get_root();
    admin_externalpage_setup('enrolment', $adminroot);

    $CFG->pagepath = 'enrol/mnet';
    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class
    $enrolment = enrolment_factory::factory('mnet');

    $mnethost = required_param('host', PARAM_INT);

    $courses = $enrolment->fetch_remote_courses($mnethost);

/// Print the page

    /// get language strings
    $str = get_strings(array('enrolmentplugins', 'configuration', 'users', 'administration'));

    admin_externalpage_print_header($adminroot);

    print_simple_box_start("center", "80%");

    print_simple_box_start("center", "60%", '', 5, 'informationbox');
    print_string("description", "enrol_mnet");
    print_simple_box_end();

    echo "<hr />";

    print ('<table align="center">');

    foreach ($courses as $course) {
        print ('<tr>'
               . "<td colspan=\"2\"><a href=\"{$CFG->wwwroot}/enrol/mnet/remote_enrolment.php?host={$mnethost}&amp;courseid={$course->id}\">{$course->fullname}</a></td>"
               . '</tr><tr>'
               . "<td align=\"left\" valign=\"top\">{$course->shortname}<br />"
               . '</td>'
               . "<td align=\"left\" >{$course->summary}</td>"
               . '</tr>');
    }
    print ('</table>');

    print_simple_box_end();

    admin_externalpage_print_footer($adminroot);

?>
