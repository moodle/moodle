<?php // $Id$
      // Allows a creator to edit groupings

require_once '../config.php';
require_once $CFG->dirroot.'/group/lib.php';

$courseid = required_param('id', PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:managegroups', $context);

$strgrouping     = get_string('grouping', 'group');
$strgroups       = get_string('groups');
$strname         = get_string('name');
$strdelete       = get_string('delete');
$stredit         = get_string('edit');
$srtnewgrouping  = get_string('creategrouping', 'group');
$strgroups       = get_string('groups');
$strgroupings    = get_string('groupings', 'group');
$struses         = get_string('activities');
$strparticipants = get_string('participants');
$strmanagegrping = get_String('showgroupsingrouping', 'group');

$navlinks = array(array('name'=>$strparticipants, 'link'=>$CFG->wwwroot.'/user/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$strgroupings, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);

/// Print header
print_header_simple($strgroupings, ': '.$strgroupings, $navigation, '', '', true, '', navmenu($course));

// Add tabs
$currenttab = 'groupings';
require('tabs.php');

print_heading($strgroupings);

$data = array();
if ($groupings = get_records('groupings', 'courseid', $course->id, 'name')) {
    foreach($groupings as $grouping) {
        $line = array();
        $line[0] = format_string($grouping->name);

        if ($groups = groups_get_all_groups($courseid, 0, $grouping->id)) {
            $groupnames = array();
            foreach ($groups as $group) {
                $groupnames[] = format_string($group->name);
            }
            $line[1] = implode(', ', $groupnames);
        } else {
            $line[1] = get_string('none');
        }
        $line[2] = (int)count_records('course_modules', 'course', $course->id, 'groupingid', $grouping->id);

        $buttons  = "<a title=\"$stredit\" href=\"grouping.php?id=$grouping->id\"><img".
                    " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        $buttons .= "<a title=\"$strdelete\" href=\"grouping.php?id=$grouping->id&amp;delete=1\"><img".
                    " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
        $buttons .= "<a title=\"$strmanagegrping\" href=\"assign.php?id=$grouping->id\"><img".
                    " src=\"$CFG->pixpath/i/group.gif\" class=\"icon\" alt=\"$strmanagegrping\" /></a> ";

        $line[3] = $buttons;
        $data[] = $line;
    }
}
$table = new object();
$table->head  = array($strgrouping, $strgroups, $struses, $stredit);
$table->size  = array('30%', '50%', '10%', '10%');
$table->align = array('left', 'left', 'center', 'center');
$table->width = '90%';
$table->data  = $data;
print_table($table);

echo '<div class="buttons">';
print_single_button('grouping.php', array('courseid'=>$courseid), $srtnewgrouping);
echo '</div>';

print_footer($course);

?>
