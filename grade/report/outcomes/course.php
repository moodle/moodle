<?php
/*********************************
 * Course outcomes editting page *
 *********************************/

include_once('../../../config.php');
require_once($CFG->libdir.'/tablelib.php');

$page = optional_param('page', 0, PARAM_INT); // current page
$search = optional_param('search', 0, PARAM_TEXT);
$deleteid = optional_param('deleteid', 0, PARAM_INT); // which outcome to delete
$confirm = optional_param('confirm', 0, PARAM_INT);
$perpage = 30;

$courseid = required_param('id', PARAM_INT); // course id
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($courseid);
require_capability('gradereport/outcomes:view', get_context_instance(CONTEXT_SYSTEM));

    /// form processing
    if ($deleteid && confirm_sesskey()) {
        require_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_COURSE, $courseid));
        if ($confirm) {
            // delete all outcomes used in courses
            // delete all outcomes used in grade items
            delete_records('grade_outcomes_courses', 'outcomeid', $deleteid);
            delete_records('grade_outcomes', 'id', $deleteid);
        } else {
            // prints confirmation
            $strgrades = get_string('grades');
            $stroutcomes = get_string('outcomes', 'grades');
            $navlinks = array();
            $navlinks[] = array('name' => $strgrades, 'link' => $CFG->wwwroot . '/grade/index.php?id='.$courseid, 'type' => 'misc');
            $navlinks[] = array('name' => $stroutcomes, 'link' => '', 'type' => 'misc');

            $navigation = build_navigation($navlinks);

/// Print header
            print_header_simple($strgrades.':'.$stroutcomes, ':'.$strgrades, $navigation, '', '', true);

            $strdeleteoutcomecheck = get_string('deleteoutcomecheck', 'grades');
            notice_yesno($strdeleteoutcomecheck,
                         'course.php?id='.$courseid.'&amp;deleteid='.$deleteid.'&amp;confirm=1&amp;sesskey='.sesskey(),
                         'course.php?id='.$courseid.'&amp;');
            print_footer();
            exit;
        }
    }

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
    $currenttab = 'courseoutcomes';
    include('tabs.php');

    /// listing of all site outcomes + this course specific outcomes
    $outcomes = get_records_sql('SELECT * FROM '.$CFG->prefix.'grade_outcomes
                                 WHERE courseid IS NULL');

    // outcomes used in this course
    $courseoutcomes = get_records_sql('SELECT go.id, go.fullname
                                       FROM '.$CFG->prefix.'grade_outcomes_courses goc,
                                            '.$CFG->prefix.'grade_outcomes go
                                       WHERE goc.courseid = '.$courseid.'
                                       AND goc.outcomeid = go.id');

    if (empty($courseoutcomes)) {
        $courseoutcomes = get_records('grade_outcomes', 'courseid', $courseid);
    } elseif ($mcourseoutcomes = get_records('grade_outcomes', 'courseid', $courseid)) {
        $courseoutcomes += $mcourseoutcomes;
    }

    check_theme_arrows();
    include_once('course.html');

    /// interface to add/edit/delete course specific outcomes
    echo '<p/>';
    print_heading(get_string('coursespecoutcome', 'gradereport_outcomes')); // course sepcific outcomes

    $totalcount = count_records('grade_outcomes_courses', 'courseid', $courseid);
    $baseurl = "course.php";
    print_paging_bar($totalcount, $page, $perpage, $baseurl);

    if ($outcomes = get_recordset('grade_outcomes', 'courseid', $courseid, '', '*', $page * $perpage, $perpage)) {

        $tablecolumns = array('outcome', 'scale', 'edit', 'usedgradeitems');
        $tableheaders = array(get_string('outcomes', 'grades'),
                              get_string('scale'),
                              '',
                              get_string('activities'));

        $table = new flexible_table('outcomes');
        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($baseurl);
        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'user-grade');
        $table->set_attribute('class', 'boxaligncenter generaltable');

        $table->setup();

        while ($outcome = rs_fetch_next_record($outcomes)) {
            $data = array();

            // full name of the outcome
            $data[] = $outcome->fullname;

            // full name of the scale used by this outcomes
            $scale= get_record('scale', 'id', $outcome->scaleid);
            $data[] = $scale->name;

            if (has_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_COURSE, $courseid))) {
            // add operations
                $data[] = '<a href="editoutcomes.php?id='.$outcome->id.'&amp;courseid='.$courseid.'&amp;sesskey='.sesskey().'"><img alt="Update" class="iconsmall" src="'.$CFG->wwwroot.'/pix/t/edit.gif"/></a>
                   <a href="course.php?deleteid='.$outcome->id.'&amp;id='.$courseid.'&amp;sesskey='.sesskey().'"><img alt="Delete" class="iconsmall" src="'.$CFG->wwwroot.'/pix/t/delete.gif"/></a>'; // icons and links
            } else {
                $data[] = '';
            }
            // num of gradeitems using this
            $num = count_records('grade_items', 'outcomeid' ,$outcome->id);
            $data[] = (int) $num;

            // num of courses using this outcome
            $table->add_data($data);
        }

        $table->print_html();
    }
    if (has_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_COURSE, $courseid))) {
        echo '<a href="editoutcomes.php?courseid='.$courseid.'">'.get_string('addoutcome', 'gradereport_outcomes').'</a>';
    }
    print_footer();

/**
 * truncates a string to a length of num
 * @param string string
 * @param int num
 * @return string
 */
function truncate($string, $num) {
    if (strlen($string) > $num + 3) {
        $text = substr($string, 0, $num);
        $text = $text."...";
    }
    return $string;
}
?>
