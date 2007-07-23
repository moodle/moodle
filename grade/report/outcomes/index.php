<?php //$Id$

include_once('../../../config.php');
require_once($CFG->libdir . '/gradelib.php');

$courseid = required_param('id');                   // course id

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course->id);

$context = get_context_instance(CONTEXT_COURSE, $course->id);

// Build navigation
$strgrades = get_string('grades');
$stroutcomes = get_string('outcomes', 'grades');
$navlinks = array();
$navlinks[] = array('name' => $strgrades, 'link' => $CFG->wwwroot . '/grade/index.php?id='.$courseid, 'type' => 'misc');
$navlinks[] = array('name' => $stroutcomes, 'link' => '', 'type' => 'misc');

$navigation = build_navigation($navlinks);

/// Print header
print_header_simple($strgrades.':'.$stroutcomes, ':'.$strgrades, $navigation, '', '', true);

// Add tabs
$currenttab = 'outcomereport';
include('tabs.php');

// Grab outcomes in use for this course
$outcomes = grade_outcome::fetch_all(array('courseid' => $courseid));
foreach ($outcomes as $outcome) {
    print_object($outcome->get_grade_info($courseid, true, true));
}
// Grab activities that are grading against each outcome (with links to activities)

// Compute average grade across all activities and users for each outcome.


print_footer($course);

?>
