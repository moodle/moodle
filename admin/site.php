<?PHP // $Id$

    require_once("../config.php");

    if ($site = get_site()) {
        require_login();
        if (!isadmin()) {
            error("You need to be admin to edit this page");
        }
        $site->format = "social";   // override
    }

/// If data submitted, then process and store.

	if ($form = data_submitted()) {

        validate_form($form, $err);

        if (count($err) == 0) {

            set_config("frontpage", $form->frontpage);

            $form->timemodified = time();

            if ($form->id) {
                if (update_record("course", $form)) {
                    redirect("$CFG->wwwroot/", get_string("changessaved"));
                } else {
                    error("Serious Error! Could not update the site record! (id = $form->id)");
                }
            } else {
                if ($newid = insert_record("course", $form)) {
                    $cat->name = get_string("miscellaneous");
                    if (insert_record("course_categories", $cat)) {
                        redirect("$CFG->wwwroot/$CFG->admin/index.php", get_string("changessaved"), 1);
                    } else {
                        error("Serious Error! Could not set up a default course category!");
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

    if ($site and empty($form)) {
        $form = $site;
        $course = $site;
        $firsttime = false;
    } else {
        $form->fullname = "";
        $form->shortname = "";
        $form->summary = "";
        $form->newsitems = 3;
        $form->id = "";
        $form->category = 0;
        $form->format = "social";
        $form->teacher = get_string("defaultcourseteacher");
        $form->teachers = get_string("defaultcourseteachers");
        $form->student = get_string("defaultcoursestudent");
        $form->students = get_string("defaultcoursestudents");
        $firsttime = true;
    }

    if (isset($CFG->frontpage)) {
        $form->frontpage = $CFG->frontpage;

    } else {
        if ($form->newsitems > 0) {
            $form->frontpage = 0;
        } else {
            $form->frontpage = 1;
        }
        set_config("frontpage", $form->frontpage);
    }

    if (empty($focus)) {
        $focus = "form.fullname";
    }

    $stradmin = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strsitesettings = get_string("sitesettings");

    if ($firsttime) {
        print_header();
        print_heading($strsitesettings);
        print_simple_box(get_string("configintrosite"), "center", "50%");
        echo "<br />";
    } else {
        print_header("$site->shortname: $strsitesettings", "$site->fullname",
                      "<a href=\"index.php\">$stradmin</a> -> ".
                      "<a href=\"configure.php\">$strconfiguration</a> -> $strsitesettings", "$focus");
        print_heading($strsitesettings);
    }

    $defaultformat = FORMAT_HTML;
    if ($usehtmleditor = can_use_richtext_editor()) {
        $onsubmit = "onsubmit=\"copyrichtext(form.summary);\"";
    } else {
        $onsubmit = "";
    }

    print_simple_box_start("center", "", "$THEME->cellheading");
    include("site.html");
    print_simple_box_end();

    if (!$firsttime) {
        print_footer();
    }

    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form(&$form, &$err) {

    if (empty($form->fullname))
        $err["fullname"] = get_string("missingsitename");

    if (empty($form->shortname))
        $err["shortname"] = get_string("missingshortsitename");

    return;
}


?>
