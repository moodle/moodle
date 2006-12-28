<?php // $Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    $streditmyprofile = get_string("editmyprofile");
    $stradministration = get_string("administration");
    $strchoose = get_string("choose");
    $struser = get_string("user");
    $strusers = get_string("users");
    $strusersnew = get_string("usersnew");
    $strimportgroups = get_string("importgroups");
    print_heading_with_help($strimportgroups, 'uploadgroups');
    $maxuploadsize = get_max_upload_file_size();
    echo '<p align="center">';
    print_simple_box_start('center','80%');

    // use formslib
    include_once('import_form.php');
    $mform_post = new course_import_groups_form($CFG->wwwroot.'/course/import/groups/index.php?id='.$id, array('maxuploadsize'=>$maxuploadsize));
    $mform_post ->display();

    print_simple_box_end();

    echo '</p>';

?>
