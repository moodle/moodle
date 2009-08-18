<?php // $Id$
/**
* print the form to map courses for global feedbacks
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/tablelib.php");

    $id = required_param('id', PARAM_INT); // Course Module ID, or
    $searchcourse = optional_param('searchcourse', '', PARAM_ALPHANUM);
    $coursefilter = optional_param('coursefilter', '', PARAM_INT);
    $courseid = optional_param('courseid', false, PARAM_INT);

    if(($formdata = data_submitted()) AND !confirm_sesskey()) {
        print_error('invalidsesskey');
    }

    // $SESSION->feedback->current_tab = 'mapcourse';
    $current_tab = 'mapcourse';

    if ($id) {
        if (! $cm = get_coursemodule_from_id('feedback', $id)) {
            print_error('invalidcoursemodule');
        }

        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error('coursemisconf');
        }

        if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    }
    $capabilities = feedback_load_capabilities($cm->id);

    require_login($course->id, true, $cm);

    if (!$capabilities->mapcourse) {
        print_error('invalidaccess');
    }

    if ($coursefilter) {
        $map->feedbackid = $feedback->id;
        $map->courseid = $coursefilter;
        // insert a map only if it does exists yet
        $sql = "SELECT id, feedbackid
                  FROM {feedback_sitecourse_map}
                 WHERE feedbackid = ? AND courseid = ?";
        if (!$DB->get_records_sql($sql, array($map->feedbackid, $map->courseid))) {
            $DB->insert_record('feedback_sitecourse_map', $map);
        }
    }

    /// Print the page header
    // $strfeedbacks = get_string("modulenameplural", "feedback");
    // $strfeedback = get_string("modulename", "feedback");
    // $navigation = '';

    // $feedbackindex = '<a href="'.htmlspecialchars('index.php?id='.$course->id).'">'.$strfeedbacks.'</a> ->';
    // if ($course->category) {
        // $navigation = '<a href="'.htmlspecialchars('../../course/view.php?id='.$course->id).'">'.$course->shortname.'</a> ->';
    // }else if ($courseid > 0 AND $courseid != SITEID) {
        // $usercourse = $DB->get_record('course', array('id'=>$courseid));
        // $navigation = '<a href="'.htmlspecialchars('../../course/view.php?id='.$usercourse->id).'">'.$usercourse->shortname.'</a> ->';
        // $feedbackindex = '';
    // }

    // print_header($course->shortname.': '.$feedback->name, $course->fullname,
                // $navigation.' '.$feedbackindex.' <a href="'.htmlspecialchars('view.php?id='.$id).'">'.$feedback->name.'</a> -> '.get_string('mapcourses', 'feedback'),
                // '', '', true, update_module_button($cm->id, $course->id, $strfeedback), navmenu($course, $cm));
    /// Print the page header
    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");
    $buttontext = update_module_button($cm->id, $course->id, $strfeedback);

    $navlinks = array();
    $navlinks[] = array('name' => $strfeedbacks, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($feedback->name), 'link' => "", 'type' => 'activityinstance');

    $navigation = build_navigation($navlinks);

    print_header_simple(format_string($feedback->name), "",
                 $navigation, "", "", true, $buttontext, navmenu($course, $cm));

    include('tabs.php');

    echo $OUTPUT->box(get_string('mapcourseinfo', 'feedback'), 'generalbox boxaligncenter boxwidthwide');
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    echo '<form method="post">';
    echo '<input type="hidden" name="id" value="'.$id.'" />';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

    $sql = "select c.id, c.shortname
              from {course} c
             where c.shortname ".$DB->sql_ilike()." ?
                   OR c.fullname ".$DB->sql_ilike()." ?";
    $params = array("%{$searchcourse}%", "%{$searchcourse}%");

    if (($courses = $DB->get_records_sql_menu($sql, $params)) && !empty($searchcourse)) {
        echo ' ' . get_string('courses') . ': ';
        echo $OUTPUT->select(html_select::make ($courses, 'coursefilter', $coursefilter));
        echo '<input type="submit" value="'.get_string('mapcourse', 'feedback').'"/>';
        echo $OUTPUT->help_icon(moodle_help_icon::make('mapcourses', '', 'feedback', true));
        echo '<input type="button" value="'.get_string('searchagain').'" onclick="document.location=\'mapcourse.php?id='.$id.'\'"/>';
        echo '<input type="hidden" name="searchcourse" value="'.$searchcourse.'"/>';
        echo '<input type="hidden" name="feedbackid" value="'.$feedback->id.'"/>';
        echo $OUTPUT->help_icon(moodle_help_icon::make('searchcourses', '', 'feedback', true));
    } else {
        echo '<input type="text" name="searchcourse" value="'.$searchcourse.'"/> <input type="submit" value="'.get_string('searchcourses').'"/>';
        echo $OUTPUT->help_icon(moodle_help_icon::make('searchcourses', '', 'feedback', true));
    }

    echo '</form>';

    if($coursemap = feedback_get_courses_from_sitecourse_map($feedback->id)) {
        $table = new flexible_table('coursemaps');
        $table->define_columns( array('course'));
        $table->define_headers( array(get_string('mappedcourses', 'feedback')));

        $table->setup();

        foreach ($coursemap as $cmap) {
            $table->add_data(array('<a href="'.htmlspecialchars('unmapcourse.php?id='.$id.'&cmapid='.$cmap->id).'"><img src="'.$OUTPUT->old_icon_url('t/delete') . '" alt="Delete" /></a> ('.$cmap->shortname.') '.$cmap->fullname));
        }

        $table->print_html();
    } else {
        echo '<h3>'.get_string('mapcoursenone', 'feedback').'</h3>';
    }


    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();

?>
