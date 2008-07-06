<?php // $Id$
      // Admin-only code to delete a course utterly

    require_once("../config.php");

    $id     = required_param('id', PARAM_INT);              // course id
    $delete = optional_param('delete', '', PARAM_ALPHANUM); // delete confirmation hash

    require_login();

    if (!can_delete_course($id)) {
        error('You do not have the permission to delete this course.');
    }

    if (!$site = get_site()) {
        error("Site not found!");
    }

    $strdeletecourse = get_string("deletecourse");
    $stradministration = get_string("administration");
    $strcategories = get_string("categories");

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    $category = get_record("course_categories", "id", $course->category);
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

        notice_yesno("$strdeletecoursecheck<br /><br />" . format_string($course->fullname) .
                     " (" . format_string($course->shortname) . ")",
                     "delete.php?id=$course->id&amp;delete=".md5($course->timemodified)."&amp;sesskey=$USER->sesskey",
                     "category.php?id=$course->category");

        print_footer($course);
        exit;
    }

    if ($delete != md5($course->timemodified)) {
        error("The check variable was wrong - try again");
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

    print_heading($strdeletingcourse);

    delete_course($course);
    fix_course_sortorder(); //update course count in catagories

    print_heading( get_string("deletedcourse", "", format_string($course->shortname)) );

    print_continue("category.php?id=$course->category");

    print_footer();

?>
