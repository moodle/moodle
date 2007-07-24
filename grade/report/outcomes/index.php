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

// Grab all outcomes, distinguishing between site-level and course-level outcomes
$sql = "SELECT mdl_grade_outcomes.id,
               mdl_grade_outcomes_courses.courseid,
               mdl_grade_outcomes.shortname,
               mdl_grade_outcomes.scaleid
          FROM mdl_grade_outcomes
     LEFT JOIN mdl_grade_outcomes_courses
            ON (mdl_grade_outcomes.id = mdl_grade_outcomes_courses.outcomeid AND mdl_grade_outcomes_courses.courseid = $courseid)
      ORDER BY mdl_grade_outcomes_courses.courseid DESC";

$report_info = array();
$outcomes = get_records_sql($sql);

// Get grade_items that use each outcome
foreach ($outcomes as $outcomeid => $outcome) {
    $sql = "SELECT mdl_grade_items.id,
                   mdl_grade_items.itemname,
                   mdl_grade_items.itemmodule,
                   mdl_grade_items.iteminstance,
                   mdl_grade_items.itemtype,
                   mdl_grade_items.itemnumber,
                   mdl_grade_items.courseid,
                   mdl_grade_items.idnumber
              FROM mdl_grade_items
             WHERE mdl_grade_items.outcomeid = $outcomeid";
    $report_info[$outcomeid]['items'] = get_records_sql($sql);
    $report_info[$outcomeid]['outcome'] = $outcome;

    // Get average grades for each item
    if (is_array($report_info[$outcomeid]['items'])) {
        foreach ($report_info[$outcomeid]['items'] as $itemid => $item) {
            $sql = "SELECT id, AVG(finalgrade) AS `avg`, COUNT(finalgrade) AS `count`
                      FROM mdl_grade_grades
                     WHERE itemid = $itemid
                  GROUP BY itemid";
            $info = get_records_sql($sql);
            $info = reset($info);
            $report_info[$outcomeid]['items'][$itemid]->avg = round($info->avg, 2);
            $report_info[$outcomeid]['items'][$itemid]->count = $info->count;
        }
    }
}

$html = '<table border="1" summary="Outcomes Report">' . "\n";
$html .= '<tr><th>' . get_string('outcomename', 'grades') . '</th>';
$html .= '<th>' . get_string('sitewide', 'grades') . '</th>';
$html .= '<th>' . get_string('activities', 'grades') . '</th>';
$html .= '<th>' . get_string('average', 'grades') . '</th>';
$html .= '<th>' . get_string('numberofgrades', 'grades') . '</th></tr>' . "\n";

foreach ($report_info as $outcomeid => $outcomedata) {
    $rowspan = count($outcomedata['items']);
    $html .= '<td rowspan="' . $rowspan . '">' . $outcomedata['outcome']->shortname . "</td>\n";

    $sitewide = get_string('no');
    if (empty($outcomedata['outcome']->courseid)) {
        $sitewide = get_string('yes');
    }

    $html .= '<td rowspan="' . $rowspan . '">' . $sitewide . "</td>\n";

    $print_tr = false;
    if (is_array($outcomedata['items'])) {
        foreach ($outcomedata['items'] as $itemid => $item) {
            if ($print_tr) {
                $html .= '<tr>';
            }

            $cm = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $item->courseid);
            $itemname = '<a href="'.$CFG->wwwroot.'/mod/'.$item->itemmodule.'/view.php?id='.$cm->id.'">'.$item->itemname.'</a>';

            // Obtain nearest scale item from average
            $scales_array = get_records_list('scale', 'id', $outcomedata['outcome']->scaleid);
            $scale = $scales_array[$outcomedata['outcome']->scaleid];
            $scales = explode(",", $scale->scale);

            // this could be a 0 when summed and rounded, e.g, 1, no grade, no grade, no grade
            if ($item->avg < 1) {
                $item->avg = 1;
            }

            $gradehtml = $scales[$item->avg-1];

            $html .= "<td>$itemname</td><td>$gradehtml ($item->avg)</td><td>$item->count</td></tr>\n";
            $print_tr = true;
        }
    } else {
        $html .= "<td> - </td><td> - </td><td> 0 </td>";
    }
}


$html .= '</table>';
echo $html;
print_footer($course);

?>
