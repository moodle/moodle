<?php // $Id$
      // Allows a creator to edit custom outcomes, and also display help about outcomes

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';

$courseid = required_param('id', PARAM_INT);

/// Make sure they can even access this course
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:update', $context);

/// form processing
if ($data = data_submitted()) {
    require_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_COURSE, $courseid));
    if (!empty($data->add) && !empty($data->addoutcomes)) {
    /// add all selected to course list
        foreach ($data->addoutcomes as $add) {
            $goc -> courseid = $courseid;
            $goc -> outcomeid = $add;
            insert_record('grade_outcomes_courses', $goc);
        }
    } else if (!empty($data->remove) && !empty($data->removeoutcomes)) {
    /// remove all selected from course outcomes list
        foreach ($data->removeoutcomes as $remove) {
            delete_records('grade_outcomes_courses', 'courseid', $courseid, 'outcomeid', $remove);
        }
    }
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'favoutcomes', 'courseid'=>$courseid));

$strgrades = get_string('grades');
$pagename  = get_string('courseoutcomes', 'grades');

$navlinks = array(array('name'=>$strgrades, 'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$pagename, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);

$outcomes = grade_outcome::fetch_all_global();
$courseoutcomes = array();
if ($coutcomes = get_records_sql('SELECT go.id, go.fullname
                                       FROM '.$CFG->prefix.'grade_outcomes_courses goc,
                                            '.$CFG->prefix.'grade_outcomes go
                                        WHERE goc.courseid = '.$courseid.'
                                       AND goc.outcomeid = go.id')) {
    foreach ($coutcomes as $id=>$coutcome) {
        $courseoutcomes[$id] = new grade_outcome(array('id'=>$id));     
    }
}

if (empty($courseoutcomes)) {
    $courseoutcomes = grade_outcome::fetch_all(array('courseid'=>$courseid));
} elseif ($mcourseoutcomes = grade_outcome::fetch_all(array('courseid'=>$courseid))) {
    $courseoutcomes += $mcourseoutcomes;
}
/// Print header
print_header_simple($strgrades.': '.$pagename, ': '.$strgrades, $navigation, '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'edit', 'courseoutcomes');

check_theme_arrows();
include_once('form.html');

print_footer($course);
?>