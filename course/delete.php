<?php // $Id$
      // Admin-only code to delete a course utterly

    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->dirroot . '/course/lib.php');

    $id     = required_param('id', PARAM_INT);              // course id
    $delete = optional_param('delete', '', PARAM_ALPHANUM); // delete confirmation hash

    $PAGE->set_url('course/delete.php', array('id' => $id));
    require_login();

    if (!can_delete_course($id)) {
        print_error('cannotdeletecourse');
    }

    if (!$site = get_site()) {
        print_error("siteisnotdefined", 'debug');
    }

    $strdeletecourse = get_string("deletecourse");
    $stradministration = get_string("administration");
    $strcategories = get_string("categories");

    if (! $course = $DB->get_record("course", array("id"=>$id))) {
        print_error("invalidcourseid");
    }

    $category = $DB->get_record("course_categories", array("id"=>$course->category));
    $navlinks = array();

    if (! $delete) {
        $strdeletecheck = get_string("deletecheck", "", $course->shortname);
        $strdeletecoursecheck = get_string("deletecoursecheck");

        $navlinks[] = array('name' => $stradministration, 'link' => "../$CFG->admin/index.php", 'type' => 'misc');
        $navlinks[] = array('name' => $strcategories, 'link' => "index.php", 'type' => 'misc');
        $navlinks[] = array('name' => $category->name, 'link' => "category.php?id=$course->category", 'type' => 'misc');
        $navlinks[] = array('name' => $strdeletecheck, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header("$site->shortname: $strdeletecheck", $site->fullname, $navigation);

        $message = "$strdeletecoursecheck<br /><br />" . format_string($course->fullname) .  " (" . format_string($course->shortname) . ")";
        echo $OUTPUT->confirm($message, "delete.php?id=$course->id&delete=".md5($course->timemodified), "category.php?id=$course->category");

        echo $OUTPUT->footer();
        exit;
    }

    if ($delete != md5($course->timemodified)) {
        print_error("invalidmd5");
    }

    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    }

    // OK checks done, delete the course now.

    add_to_log(SITEID, "course", "delete", "view.php?id=$course->id", "$course->fullname (ID $course->id)");

    $strdeletingcourse = get_string("deletingcourse", "", format_string($course->shortname));

    $navlinks[] = array('name' => $stradministration, 'link' => "../$CFG->admin/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strcategories, 'link' => "index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $category->name, 'link' => "category.php?id=$course->category", 'type' => 'misc');
    $navlinks[] = array('name' => $strdeletingcourse, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("$site->shortname: $strdeletingcourse", $site->fullname, $navigation);

    echo $OUTPUT->heading($strdeletingcourse);

    delete_course($course);
    fix_course_sortorder(); //update course count in catagories

    echo $OUTPUT->heading( get_string("deletedcourse", "", format_string($course->shortname)) );

    echo $OUTPUT->continue_button("category.php?id=$course->category");

    echo $OUTPUT->footer();

?>
