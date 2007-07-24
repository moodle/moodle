<?php

include_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

// setting up params
$id = optional_param('id', 0, PARAM_INT); // outcomes id
$page = optional_param('page', 0, PARAM_INT); // current page
$search = optional_param('search', 0, PARAM_TEXT);
$deleteid = optional_param('deleteid', 0, PARAM_INT); // which outcome to delete
$confirm = optional_param('confirm', 1, PARAM_INT);
$perpage = 30;

// form processing
if ($frm = data_submitted() && confirm_sesskey() && $deleteid) {
    if ($confirm) {
        // delete all outcomes used in courses
        // delete all outcomes used in grade items
    } else {
        // prints confirmation
    }
}

/// display information
admin_externalpage_setup('gradereportoutcomes');
admin_externalpage_print_header();

/******************* ADD TABS HERE LATER ****************************/
// Add tabs
$currenttab = 'outcomesettings';
include('tabs.php');

$totalcount = count_records('grade_outcomes');
$baseurl = "settings.php";
print_paging_bar($totalcount, $page, $perpage, $baseurl);

if ($outcomes = get_recordset('grade_outcomes', '', '', '', '*', $page * $perpage, $perpage)) {

    $tablecolumns = array('outcome', 'edit', 'usedgradeitems', 'usedcourses');
    $tableheaders = array(get_string('outcomes', 'grades'),
                          get_string('operations', 'grades'),
                          get_string('usedgradeitem', 'grades'),
                          get_string('usedcourses', 'grades'));

    $table = new flexible_table('outcomes');
    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($baseurl);
    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'user-grade');
    $table->set_attribute('class', 'boxaligncenter generaltable');

    $table->setup();

    foreach ($outcomes as $outcome) {
        $data = array();
        $data[] = $outcome['fullname'];

        // add operations
        $data[] = '<a href="editoutcomes.php?id='.$outcome['id'].'"><img alt="Update" class="iconsmall" src="'.$CFG->wwwroot.'/pix/t/edit.gif"/></a>
                   <a href="settings.php?deleteid='.$outcome['id'].'"><img alt="Delete" class="iconsmall" src="'.$CFG->wwwroot.'/pix/t/delete.gif"/></a>'; // icons and links

        // num of gradeitems using this
        $num = count_records('grade_outcomes_courses', 'outcomeid' ,$outcome['id']);
        $data[] = (int) $num;

        // num of courses using this outcome
        $num = count_records('grade_items', 'outcomeid', $outcome['id']);
        $data[] = (int) $num;

        $table->add_data($data);
    }

    $table->print_html();
}

echo '<a href="editoutcomes.php">Add a new outcome</a>';

// print the footer, end of page
admin_externalpage_print_footer();
?>
