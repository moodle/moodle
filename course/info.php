<?PHP // $Id$

/// Displays external information about a course

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);    // Course id
    optional_variable($name);

    if (!$id and !$name) {
        error("Must specify course id or short name");
    }

    if ($name) {
        if (! $course = get_record("course", "shortname", $name) ) {
            error("That's an invalid short course name");
        }
    } else {
        if (! $course = get_record("course", "id", $id) ) {
            error("That's an invalid course id");
        }
    }

    $site = get_site();

    if ($CFG->forcelogin) {
        require_login();
    }

    if (empty($THEME->custompix)) {
        $pixpath = "$CFG->wwwroot/pix";
    } else {
        $pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
    }

    print_header(get_string("summaryof", "", $course->fullname));

    echo "<h3 align=\"center\">$course->fullname<br />($course->shortname)</h3>";

    echo "<center>";
    if ($course->guest) {
        $strallowguests = get_string("allowguests");
        echo "<p><font size=1><img align=\"absmiddle\" alt=\"\" height=16 width=16 border=0 src=\"$pixpath/i/guest.gif\"></a>&nbsp;$strallowguests</font></p>";
    }
    if ($course->password) {
        $strrequireskey = get_string("requireskey");
        echo "<p><font size=1><img align=\"absmiddle\" alt=\"\" height=16 width=16 border=0 src=\"$pixpath/i/key.gif\"></a>&nbsp;$strrequireskey</font></p>";
    }


    if ($teachers = get_course_teachers($course->id)) {
        echo "<table align=center><tr><td nowrap>";
        echo "<p><font size=\"1\">\n";
        foreach ($teachers as $teacher) {
            if ($teacher->authority > 0) {
                if (!$teacher->role) {
                    $teacher->role = $course->teacher;
                }
                echo "$teacher->role: ";
                link_to_popup_window ("/user/view.php?id=$teacher->id&course=$site->id", "opener", 
                                      fullname($teacher), 400, 500, "$teacher->role", 
                                      "");
                echo "<br />";
            }
        }
        echo "</font></p>";
        echo "</td</tr></table>";
    }
    echo "<br />";

    print_simple_box_start("center", "100%");
    echo filter_text(text_to_html($course->summary),$course->id);
    print_simple_box_end();

    echo "<br />";

    close_window_button();

?>

