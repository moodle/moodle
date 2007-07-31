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

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'favoutcomes', 'courseid'=>$courseid));

$strgrades = get_string('grades');
$pagename  = get_string('favoutcomes', 'grades');

$navlinks = array(array('name'=>$strgrades, 'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$pagename, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);

if ($fav_outcomes_ids = get_user_preferences('grade_favourite_outcomes', null)) {
    $fav_outcomes_ids = unserialize($fav_outcomes_ids);
} else {
    $fav_outcomes_ids = array();
}

$outcomes = grade_outcome::fetch_all_global();
$favoutcomes = array();
foreach($outcomes as $outcome) {
    if (in_array($outcome->id, $fav_outcomes_ids)) {
        unset($outcomes[$outcome->id]);
        $favoutcomes[$outcome->id] = $outcome;
    }
}

// store user preferences
if ($data = data_submitted()) {
    if (!empty($data->add) && !empty($data->addoutcomes)) {
    /// add all selected to favourite list
        foreach ($data->addoutcomes as $add) {
            if (isset($outcomes[$add])) {
                $outcome = $outcomes[$add];
                unset($outcomes[$outcome->id]);
                $favoutcomes[$outcome->id] = $outcome;
            }
        }
    } else if (!empty($data->remove) && !empty($data->removeoutcomes)) {
    /// remove all selected from favourites
        foreach ($data->removeoutcomes as $remove) {
            if (isset($favoutcomes[$remove])) {
                $outcome = $favoutcomes[$remove];
                unset($favoutcomes[$outcome->id]);
                $outcomes[$outcome->id] = $outcome;
            }
        }
    }

    $pref = array_keys($favoutcomes);
    $pref = serialize($pref);
    set_user_preference('grade_favourite_outcomes', $pref);
}

/// Print header
print_header_simple($strgrades.': '.$pagename, ': '.$strgrades, $navigation, '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'edit', 'favoutcomes');


check_theme_arrows();
include_once('form.html');

print_footer($course);


?>
