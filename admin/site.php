<?PHP // $Id$

	require("../config.php");

    $course = get_site();

/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

        $form = (object)$HTTP_POST_VARS;

        validate_form($form, $err);

        if (count($err) == 0) {

            $form->timemodified = time();

            if ($form->id) {
                if (update_record("course", $form)) {
		            redirect("$CFG->wwwroot/admin/", "Changes saved");
                } else {
                    error("Serious Error! Could not update the course record! (id = $form->id)");
                }
            } else {
                if ($newid = insert_record("course", $form)) {
                    $cat->name = "General";
                    if (insert_record("course_categories", $cat)) {
		                redirect("$CFG->wwwroot/admin/", "Changes saved", "1");
                    } else {
                        error("Serious Error! Could not set up the default categories!");
                    }
                } else {
                    error("Serious Error! Could not set up the site!");
                }
            }
		    die;
        } else {
            foreach ($err as $key => $value) {
                $focus = "form.$key";
            }
            
        }
	}

/// Otherwise fill and print the form.

    if ($course && !$form) {
        $form = $course;
    } else {
        $form->category = 0;
        $form->newsitems = 1;
    }

    print_header("Admin: Setting up site", "Administration: Setting up site",
                  "<A HREF=\"$CFG->wwwroot/admin/\">Admin</A> -> Setting up site", "$focus");

    print_simple_box_start("center", "", "$THEME->cellheading");
    print_heading("Editing site settings");
	include("site.html");
    print_simple_box_end();
    print_footer();

    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form(&$form, &$err) {

    if (empty($form->fullname))
        $err["fullname"] = "Missing site name";

    if (empty($form->shortname))
        $err["shortname"] = "Missing short site name";

    if (empty($form->summary))
        $err["summary"] = "Missing site description";

    return;
}


?>
