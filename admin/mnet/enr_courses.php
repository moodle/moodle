<?PHP
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once(dirname(__FILE__) . "/../../config.php");
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/mnet/lib.php');

    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    }


    admin_externalpage_setup('mnetenrol');

    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class
    $enrolment = enrolment_factory::factory('mnet');

    $mnethost = required_param('host', PARAM_INT);
    $host = $DB->get_record('mnet_host', array('id'=>$mnethost));

    $courses = $enrolment->fetch_remote_courses($mnethost);

    /// Print the page

    echo $OUTPUT->header();

    echo $OUTPUT->box('<strong>' . s($host->name) . ' </strong><br />'
              . get_string("enrolcourses_desc", "mnet"));

    echo '<hr />';

    echo '<div id="trustedhosts"><!-- See theme/standard/styles_layout.css #trustedhosts .generaltable for rules -->'
           . '<table class="generaltable">';

    $icon  = "<img src=\"" . $OUTPUT->pix_url('i/course') . "\"".
    " class=\"icon\" alt=\"".get_string("course")."\" />";

    foreach ($courses as $course) {
        $link = "$CFG->wwwroot/$CFG->admin/mnet/enr_course_enrol.php?"
            . "host={$mnethost}&amp;courseid={$course->id}&amp;sesskey=".sesskey();
        echo '<tr>'
               . "<td>$icon</td>"
               . "<td><a href=\"$link\">".format_string($course->fullname). "</a></td>"
               . '</tr><tr>'
               . '<td></td>'
               . '<td>'.format_string($course->shortname). ' - ' .format_string($course->cat_name).'</td>'
               . '</tr><tr>'
               . '<td></td>'
               . "<td align=\"left\" >{$course->summary}</td>"
               . '</tr>';
    }
    echo '</table></div>';

    echo $OUTPUT->footer();
