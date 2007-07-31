<?php //$Id$

include_once('../../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once $CFG->dirroot.'/grade/lib.php';

$courseid = required_param('id', PARAM_INT);                   // course id

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course->id);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

require_capability('gradereport/outcomes:view', $context);

// Build navigation
$strgrades = get_string('grades');
$stroutcomes = get_string('outcomes', 'grades');
$navlinks = array();
$navlinks[] = array('name' => $strgrades, 'link' => $CFG->wwwroot . '/grade/index.php?id='.$courseid, 'type' => 'misc');
$navlinks[] = array('name' => $stroutcomes, 'link' => '', 'type' => 'misc');

$navigation = build_navigation($navlinks);

/// Print header
print_header_simple($strgrades.':'.$stroutcomes, ':'.$strgrades, $navigation, '', '', true);
print_grade_plugin_selector($courseid, 'report', 'outcomes');

//first make sure we have proper final grades
grade_regrade_final_grades($courseid);

// Grab all outcomes used in course
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
    $report_info[$outcomeid]['items'] = get_records_select('grade_items', "outcomeid = $outcomeid AND courseid = $courseid");
    $report_info[$outcomeid]['outcome'] = $outcome;

    // Get average grades for each item
    if (is_array($report_info[$outcomeid]['items'])) {
        foreach ($report_info[$outcomeid]['items'] as $itemid => $item) {
            $sql = "SELECT id, AVG(finalgrade) AS `avg`, COUNT(finalgrade) AS `count`
                      FROM {$CFG->prefix}grade_grades
                     WHERE itemid = $itemid
                  GROUP BY itemid";
            $info = get_records_sql($sql);

            if (!$info) {
                unset($report_info[$outcomeid]['items'][$itemid]);
                continue;
            } else {
                $info = reset($info);
                $avg = round($info->avg, 2);
                $count = $info->count;
            }

            $report_info[$outcomeid]['items'][$itemid]->avg = $avg;
            $report_info[$outcomeid]['items'][$itemid]->count = $count;
        }
    }
}

$html = '<table class="generaltable boxaligncenter" width="90%" cellspacing="1" cellpadding="5" summary="Outcomes Report">' . "\n";
$html .= '<tr><th class="header c0" scope="col">' . get_string('outcomename', 'grades') . '</th>';
$html .= '<th class="header c1" scope="col">' . get_string('overallavg', 'grades') . '</th>';
$html .= '<th class="header c2" scope="col">' . get_string('sitewide', 'grades') . '</th>';
$html .= '<th class="header c3" scope="col">' . get_string('activities', 'grades') . '</th>';
$html .= '<th class="header c4" scope="col">' . get_string('average', 'grades') . '</th>';
$html .= '<th class="header c5" scope="col">' . get_string('numberofgrades', 'grades') . '</th></tr>' . "\n";

$row = 0;
foreach ($report_info as $outcomeid => $outcomedata) {
    $rowspan = count($outcomedata['items']);
    $shortname_html = '<tr class="r' . $row . '"><td class="cell c0" rowspan="' . $rowspan . '">' . $outcomedata['outcome']->shortname . "</td>\n";

    $sitewide = get_string('no');
    if (empty($outcomedata['outcome']->courseid)) {
        $sitewide = get_string('yes');
    }

    $sitewide_html = '<td class="cell c2" rowspan="' . $rowspan . '">' . $sitewide . "</td>\n";

    $outcomedata['outcome']->sum = 0;
    $scale = new grade_scale(array('id' => $outcomedata['outcome']->scaleid), false);

    $print_tr = false;
    $items_html = '';

    if (is_array($outcomedata['items'])) {
        foreach ($outcomedata['items'] as $itemid => $item) {
            if ($print_tr) {
                $row++;
                $items_html .= "<tr class=\"r$row\">\n";
            }

            $grade_item = new grade_item($item, false);

            if ($item->itemtype == 'mod') {
                $cm = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $item->courseid);
                $itemname = '<a href="'.$CFG->wwwroot.'/mod/'.$item->itemmodule.'/view.php?id='.$cm->id.'">'.$grade_item->get_name().'</a>';
            } else {
                $itemname = $grade_item->get_name();
            }

            $outcomedata['outcome']->sum += $item->avg;
            $gradehtml = $scale->get_nearest_item($item->avg);

            $items_html .= "<td class=\"cell c3\">$itemname</td>"
                         . "<td class=\"cell c4\">$gradehtml ($item->avg)</td>"
                         . "<td class=\"cell c5\">$item->count</td></tr>\n";
            $print_tr = true;
        }
    } else {
        $items_html .= "<td class=\"cell c3\"> - </td><td class=\"cell c4\"> - </td><td class=\"cell c5\"> 0 </td></tr>\n";
    }

    // Calculate outcome average
    if (is_array($outcomedata['items'])) {
        $avg = $outcomedata['outcome']->sum / count($outcomedata['items']);
        $avg_html = $scale->get_nearest_item($avg) . " (" . round($avg, 2) . ")\n";
    } else {
        $avg_html = ' - ';
    }

    $outcomeavg_html = '<td class="cell c1" rowspan="' . $rowspan . '">' . $avg_html . "</td>\n";

    $html .= $shortname_html . $outcomeavg_html . $sitewide_html . $items_html;
    $row++;
}



$html .= '</table>';
print_heading($stroutcomes);

echo $html;
print_footer($course);

?>
