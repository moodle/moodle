<?PHP // $Id$
      // Allows the admin to create, delete and rename course categories

	require_once("../config.php");
	require_once("lib.php");

    require_login();

    if (!isadmin()) {
        error("Only administrators can use this course!");
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }


/// Print headings

    $stradministration = get_string("administration");
    $strcategories = get_string("categories");
    $strcategory = get_string("category");
    $strcourses = get_string("courses");
    $stredit = get_string("edit");
    $strdelete = get_string("delete");
    $straction = get_string("action");
    $stradd = get_string("add");

	print_header("$site->shortname: $strcategories", "$site->fullname", 
                 "<A HREF=\"../$CFG->admin/index.php\">$stradministration</A> -> $strcategories");

    print_heading($strcategories);

/// If data submitted, then process and store.

	if ($form = data_submitted()) {

        $categories = array();

        // Peel out all the data from variable names.
        foreach ($form as $key => $val) {
            if ($key == "new") {
                if (!empty($val)) {
                    if (get_records("course_categories", "name", $val)) {
                        notify(get_string("categoryduplicate", "", $val));
                    } else {
                        $cat->name = $val;
                        if (!insert_record("course_categories", $cat)) {
                            error("Could not insert the new category '$val'");
                        } else {
                            notify(get_string("categoryadded", "", $val));
                        }
                    }
                }
            
            } else {
                $cat->id   = substr($key,1);
                $cat->name = $val;

                if ($existingcats = get_records("course_categories", "name", $val)) {
                    foreach($existingcats as $existingcat) {
                        if ($existingcat->id != $cat->id) {
                            notify(get_string("categoryduplicate", "", $val));
                            continue 2;
                        }
                    }
                }

                if (!update_record("course_categories", $cat)) {
                    error("Could not update the category '$val'");
                }
            }
        }
	}


/// Get the existing categories
    if (!$categories = get_categories()) {
        // Try and make one
        $cat->name = get_string("miscellaneous");
        if ($cat->id = insert_record("course_categories", $cat)) {
            $categories[$cat->id] = $cat;
        } else {
            error("Serious error: Could not create a default category!");
        }
    }

/// Delete category if the user wants to delete it
    if (isset($delete)) {
        if (delete_records("course_categories", "id", $delete)) {
            notify(get_string("categorydeleted", "", $categories[$delete]->name));
            unset($categories[$delete]);
        } else {
            error("An error occurred while trying to delete a category");
        }
    }

/// Find lowest ID category - this is the default category
    $default = 99999;
    foreach ($categories as $category) {
        if ($category->id < $default) {
            $default = $category->id;
        }
    }

/// Find any orphan courses that don't yet have a valid category and set to default
    if ($courses = get_courses()) {
        foreach ($courses as $course) {
            if (!isset( $categories[$course->category] )) {
                set_field("course", "category", $default, "id", $course->id);
            }
        }
    }


/// Print the table of all categories
    $table->head  = array ($strcategory, $strcourses, $straction);
    $table->align = array ("LEFT", "CENTER", "CENTER");
    $table->size = array ("80", "50", "50");
    $table->width = 100;

    echo "<FORM ACTION=categories.php METHOD=post>";
    foreach ($categories as $category) {
        $count = count_records("course", "category", $category->id);
        if ($category->id == $default) { 
            $delete = "";  // Can't delete default category
        } else {
            $delete = "<A HREF=\"categories.php?delete=$category->id\">$strdelete</A>";
        }
        $table->data[] = array ("<INPUT TYPE=text NAME=\"c$category->id\" VALUE=\"$category->name\" SIZE=30>",
                                "<A HREF=\"index.php?category=$category->id\">$count</A>", $delete);
    }
    $table->data[] = array ("<INPUT TYPE=text NAME=\"new\" VALUE=\"\" SIZE=30>", "", "$stradd");
    print_table($table);
    echo "<CENTER><BR><INPUT TYPE=submit VALUE=\"".get_string("savechanges")."\"> ";
    echo "</CENTER>";
    echo "</FORM>";

    print_footer();

?>
