<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $resource = get_record("resource", "id", $cm->instance)) {
        error("Resource ID was incorrect");
    }

    $strresource = get_string("modulename", "resource");
    $strresources = get_string("modulenameplural", "resource");
    $strlastmodified = get_string("lastmodified");

    if ($course->category) {
        require_login($course->id);
        $navigation = "<A TARGET=_top HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->
                       <A TARGET=_top HREF=\"index.php?id=$course->id\">$strresources</A> ->";
    } else {
        $navigation = "<A TARGET=_top HREF=\"index.php?id=$course->id\">$strresources</A> ->";
    }

    switch ($resource->type) {
        case 1:  // Reference (eg Journal or Book etc)
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource));

            print_simple_box($resource->reference, "center");
            echo "<CENTER><P>";
            echo text_to_html($resource->summary);
            echo "</P>";
            echo "<P>&nbsp</P>";
            echo "<P><FONT SIZE=1>$strlastmodified: ".userdate($resource->timemodified)."</P>";
            echo "</CENTER>";
            print_footer($course);
            break;

        case 5: // Web Link
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            redirect($resource->reference);
            break;

        case 2: // Web Page
            if ($frameset) {
                print_header("$course->shortname: $resource->name", "$course->fullname", 
                "$navigation <A TARGET=_top HREF=\"$resource->reference\" TITLE=\"$resource->reference\">$resource->name</A>",
                "", "", true, update_module_button($cm->id, $course->id, $strresource));
                echo "<CENTER><FONT SIZE=-1>".text_to_html($resource->summary, true, false)."</FONT></CENTER>";

            } else {
                add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
                echo "<HEAD><TITLE>$course->shortname: $resource->name</TITLE></HEAD>\n";
                echo "<FRAMESET ROWS=130,*>";
                echo "<FRAME SRC=\"view.php?id=$cm->id&frameset=true\">";
                echo "<FRAME SRC=\"$resource->reference\">";
                echo "</FRAMESET>";
            }
            break;

        case 3:  // Uploaded File
            if ($frameset) {
                print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource));
                echo "<CENTER><FONT SIZE=-1>".text_to_html($resource->summary, true, false)."</FONT></CENTER>";

            } else {
                add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
                if ($CFG->slasharguments) {
                    $ffurl = "file.php/$course->id/$resource->reference";
                } else {
                    $ffurl = "file.php?file=/$course->id/$resource->reference";
                }
                echo "<HEAD><TITLE>$course->shortname: $resource->name</TITLE></HEAD>\n";
                echo "<FRAMESET ROWS=130,*>";
                echo "<FRAME SRC=\"view.php?id=$cm->id&frameset=true\">";
                echo "<FRAME SRC=\"$CFG->wwwroot/$ffurl\">";
                echo "</FRAMESET>";
            }
            break;

        case 4:  // Plain text
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource));

            print_simple_box(text_to_html($resource->alltext), "CENTER", "", "$THEME->cellcontent", "20");

            echo "<CENTER><P><FONT SIZE=1>$strlastmodified: ".userdate($resource->timemodified)."</P></CENTER>";

            print_footer($course);
            break;

        case 6:  // HTML text
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource));

            print_simple_box_start("CENTER", "", "$THEME->cellcontent", "20");

            echo $resource->alltext;

            print_simple_box_end();

            echo "<CENTER><P><FONT SIZE=1>$strlastmodified: ".userdate($resource->timemodified)."</P></CENTER>";

            print_footer($course);
            break;

        default:
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource));
            print_heading($resource->name);

            print_simple_box("Error: unknown type of resource", "center");

            print_footer($course);
            break;
    }


?>
