<?php // $Id$

    require_once('../../../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('reportquestion');

    admin_externalpage_print_header();
    print_heading(get_string('adminreport', 'question'));

    print_box(get_string('noprobs', 'question'), 'boxwidthnarrow boxaligncenter generalbox');
    admin_externalpage_print_footer();
?>
