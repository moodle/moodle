<?PHP // $Id$
      // Allows a teacher to create, edit and delete categories

	require_once("../../config.php");
	require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    if (isset($backtoquiz)) {
        redirect("edit.php");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can use this page!");
    }


/// Print headings

    $strcategory = get_string("category", "quiz");
    $strcategoryinfo = get_string("categoryinfo", "quiz");
    $strquestions = get_string("questions", "quiz");
    $strpublish = get_string("publish", "quiz");
    $strdelete = get_string("delete");
    $straction = get_string("action");
    $stradd = get_string("add");
    $strcancel = get_string("cancel");
    $strsavechanges = get_string("savechanges");
    $strbacktoquiz = get_string("backtoquiz", "quiz");

    $streditingquiz = get_string(isset($modform->instance) ? "editingquiz"
                                                           : "editquestions",
                                 "quiz");
    $streditcategories = get_string("editcategories", "quiz");

    print_header("$course->shortname: $streditcategories", "$course->shortname: $streditcategories",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                   -> <A HREF=\"edit.php\">$streditingquiz</A> -> $streditcategories");


/// Delete category if the user wants to delete it

    if (isset($delete) and !isset($cancel)) {
        if (!$category = get_record("quiz_categories", "id", $delete)) {  // security
            error("No such category $delete!");
        }

        if (isset($confirm)) { // Need to move some questions before deleting the category
            if (!$category2 = get_record("quiz_categories", "id", $confirm)) {  // security
                error("No such category $confirm!");
            }
            if (! quiz_move_questions($category->id, $category2->id)) {
                error("Error while moving questions from category '$category->name' to '$category2->name'");
            }

        } else {
            if ($count = count_records("quiz_questions", "category", $category->id)) {
                $vars->name = $category->name;
                $vars->count = $count;
                print_simple_box(get_string("categorymove", "quiz", $vars), "CENTER");
                $categories = quiz_get_category_menu($course->id);
                unset($categories[$category->id]);
                echo "<CENTER><P><FORM ACTION=category.php METHOD=get>";
                echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
                echo "<INPUT TYPE=hidden NAME=delete VALUE=\"$category->id\">";
                choose_from_menu($categories, "confirm", "", "");
                echo "<INPUT TYPE=submit VALUE=\"".get_string("categorymoveto", "quiz")."\">";
                echo "<INPUT TYPE=submit NAME=cancel VALUE=\"$strcancel\">";
                echo "</FORM></P></CENTER>";
                print_footer($course);
                exit;
            }
        }
        delete_records("quiz_categories", "id", $category->id);
        notify(get_string("categorydeleted", "", $category->name));
    }

/// Print heading

    echo "<P ALIGN=CENTER><FONT SIZE=3>";
    echo $streditcategories;
    helpbutton("categories", $streditcategories, "quiz");
    echo "</FONT></P>";

/// If data submitted, then process and store.

    if ($form = data_submitted()) {

        $form = (array)$form;

        // Peel out all the data from variable names.
        foreach ($form as $key => $val) {
            $cat = NULL;
            if ($key == "new" and $val != "") {
                $cat->name = $val;
                $cat->info = $form['newinfo'];
                $cat->publish = $form['newpublish'];
                $cat->course = $course->id;
                $cat->stamp = make_unique_id_code();
                if (!insert_record("quiz_categories", $cat)) {
                    error("Could not insert the new quiz category '$val'");
                } else {
                    notify(get_string("categoryadded", "", $val));
                }
            
            } else if (substr($key,0,1) == "c") {
                $cat->id  = substr($key,1);
                $cat->name = $val;
                $cat->info = $form["i$cat->id"];
                $cat->publish = $form["p$cat->id"];
                $cat->course = $course->id;
                if (!update_record("quiz_categories", $cat)) {
                    error("Could not update the quiz category '$val'");
                }
            }
        }
	}


/// Get the existing categories
    if (!$categories = get_records("quiz_categories", "course", $course->id, "id ASC")) {
        unset($categories);
        if (!$categories[] = quiz_get_default_category($course->id)) {
            error("Error: Could not find or make a category!");
        }
    }


/// Find lowest ID category - this is the default category
    $default = 99999;
    foreach ($categories as $category) {
        if ($category->id < $default) {
            $default = $category->id;
        }
    }


    $publishoptions[0] = get_string("no");
    $publishoptions[1] = get_string("yes");


/// Print the table of all categories
    $table->head  = array ($strcategory, $strcategoryinfo, $strpublish, $strquestions, $straction);
    $table->align = array ("LEFT", "LEFT", "CENTER", "CENTER", "CENTER");
    $table->size = array ("80", "80", "40", "40", "50");
    $table->width = 200;
    $table->nowrap = true;

    echo "<FORM ACTION=category.php METHOD=post>";
    foreach ($categories as $category) {
        $count = count_records("quiz_questions", "category", $category->id);
        if ($category->id == $default) { 
            $delete = "";  // Can't delete default category
        } else {
            $delete = "<A HREF=\"category.php?id=$course->id&delete=$category->id\">$strdelete</A>";
        }
        $table->data[] = array ("<INPUT TYPE=text NAME=\"c$category->id\" VALUE=\"$category->name\" SIZE=15>",
                                "<INPUT TYPE=text NAME=\"i$category->id\" VALUE=\"$category->info\" SIZE=50>",
                                choose_from_menu ($publishoptions, "p$category->id", "$category->publish", "", "", "", true),
                                "$count", 
                                $delete);
    }
    $table->data[] = array ("<INPUT TYPE=text NAME=\"new\" VALUE=\"\" SIZE=15>", 
                            "<INPUT TYPE=text NAME=\"newinfo\" VALUE=\"\" SIZE=50>", 
                            choose_from_menu ($publishoptions, "newpublish", "", "", "", "", true),
                            "", 
                            "$stradd");
    print_table($table);
    echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
    echo "<CENTER><BR><INPUT TYPE=submit VALUE=\"$strsavechanges\"> ";
    echo "<BR><BR><INPUT TYPE=submit NAME=backtoquiz VALUE=\"$strbacktoquiz\"> ";
    echo "</CENTER>";
    echo "</FORM>";

    print_footer();

?>
