<?php
/*********************************
 * Global outcomes editting page *
 *********************************/

include_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

// setting up params
$courseid = optional_param('id', SITEID, PARAM_INT); // course id
require_capability('gradereport/outcomes:view', get_context_instance(CONTEXT_SYSTEM));
/// check capability

$page = optional_param('page', 0, PARAM_INT); // current page
$search = optional_param('search', 0, PARAM_TEXT);
$deleteid = optional_param('deleteid', 0, PARAM_INT); // which outcome to delete
$confirm = optional_param('confirm', 0, PARAM_INT);
$perpage = 30;

    // form processing
    if ($deleteid && confirm_sesskey()) {
        require_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_SYSTEM));
        if ($confirm) {
            // delete all outcomes used in courses
            // delete all outcomes used in grade items
            delete_records('grade_outcomes_courses', 'outcomeid', $deleteid);
            delete_records('grade_outcomes', 'id', $deleteid);
        } else {
            $strgrades = get_string('grades');
            $stroutcomes = get_string('outcomes', 'grades');
            $navlinks = array();
            $navlinks[] = array('name' => $strgrades, 'link' => $CFG->wwwroot . '/grade/index.php?id='.$courseid, 'type' => 'misc');
            $navlinks[] = array('name' => $stroutcomes, 'link' => '', 'type' => 'misc');

            $navigation = build_navigation($navlinks);

/// Print header
            print_header_simple($strgrades.':'.$stroutcomes, ':'.$strgrades, $navigation, '', '', true);
            // prints confirmation
            $strdeleteoutcomecheck = get_string('deleteoutcomecheck', 'grades');
            notice_yesno($strdeleteoutcomecheck,
                         'site.php?deleteid='.$deleteid.'&amp;confirm=1&amp;sesskey='.sesskey(),
                         'site.php');
            print_footer();
            exit;
        }
    }

   /// display information
    admin_externalpage_setup('gradereportoutcomes');
    admin_externalpage_print_header();

    // Add tabs
    $currenttab = 'siteoutcomes';
    include('tabs.php');

    $totalcount = count_records('grade_outcomes');
    $baseurl = "site.php";
    print_paging_bar($totalcount, $page, $perpage, $baseurl);

    if ($outcomes = get_recordset('grade_outcomes', '', '', '', '*', $page * $perpage, $perpage)) {

        $tablecolumns = array('outcome', 'scale', 'course', 'edit', 'usedgradeitems', 'usedcourses');
        $tableheaders = array(get_string('outcomes', 'grades'),
                              get_string('scale'),
                              get_string('course'),
                              '',
                              get_string('activities'),
                              get_string('courses'));

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

            // get course
            if ($outcome->courseid) {
                $course = get_record('course', 'id', $outcome->courseid);
                $data[] = $course->shortname;
            } else {
                $data[] = get_string('site');
            }

            // add operations
            if (has_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_SYSTEM))) {

                $data[] = '<a href="editoutcomes.php?id='.$outcome->id.'&amp;sesskey='.sesskey().'"><img alt="Update" class="iconsmall" src="'.$CFG->wwwroot.'/pix/t/edit.gif"/></a>
                   <a href="site.php?deleteid='.$outcome->id.'&amp;sesskey='.sesskey().'"><img alt="Delete" class="iconsmall" src="'.$CFG->wwwroot.'/pix/t/delete.gif"/></a>'; // icons and links

            } else {
                $data[] = '';
            }
            // num of gradeitems using this
            $num = count_records('grade_items', 'outcomeid' ,$outcome->id);
            $data[] = (int) $num;

            // num of courses using this outcome
            $num = count_records('grade_outcomes_courses', 'outcomeid', $outcome->id);
            $data[] = (int) $num;

            $table->add_data($data);
        }

        $table->print_html();
    }
    if (has_capability('gradereport/outcomes:manage', get_context_instance(CONTEXT_SYSTEM))) {
        echo '<a href="editoutcomes.php">'.get_string('addoutcome', 'gradereport_outcomes').'</a>';
    }
    // print the footer, end of page
    admin_externalpage_print_footer();
?>
