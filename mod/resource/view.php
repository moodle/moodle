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
        case REFERENCE:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box($resource->reference, "center");
            echo "<CENTER><P>";
            echo text_to_html($resource->summary);
            echo "</P>";
            echo "<P>&nbsp</P>";
            echo "<P><FONT SIZE=1>$strlastmodified: ".userdate($resource->timemodified)."</P>";
            echo "</CENTER>";
            print_footer($course);
            break;

        case WEBLINK:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            redirect($resource->reference);
            break;

        case WEBPAGE:
            if ($frameset) {
                print_header("$course->shortname: $resource->name", "$course->fullname", 
                "$navigation <A TARGET=_top HREF=\"$resource->reference\" TITLE=\"$resource->reference\">$resource->name</A>",
                "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));
                echo "<CENTER><FONT SIZE=-1>".text_to_html($resource->summary, true, false)."</FONT></CENTER>";

            } else {
                add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
                echo "<HEAD><TITLE>$course->shortname: $resource->name</TITLE></HEAD>\n";
                echo "<FRAMESET ROWS=$RESOURCE_FRAME_SIZE,*>";
                echo "<FRAME SRC=\"view.php?id=$cm->id&frameset=true\">";
                echo "<FRAME SRC=\"$resource->reference\">";
                echo "</FRAMESET>";
            }
            break;

        case UPLOADEDFILE:
            if ($frameset) {
                print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));
                echo "<CENTER><FONT SIZE=-1>".text_to_html($resource->summary, true, false)."</FONT></CENTER>";

            } else {
                add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
                if ($CFG->slasharguments) {
                    $ffurl = "file.php/$course->id/$resource->reference";
                } else {
                    $ffurl = "file.php?file=/$course->id/$resource->reference";
                }
                echo "<HEAD><TITLE>$course->shortname: $resource->name</TITLE></HEAD>\n";
                echo "<FRAMESET ROWS=$RESOURCE_FRAME_SIZE,*>";
                echo "<FRAME SRC=\"view.php?id=$cm->id&frameset=true\">";
                echo "<FRAME SRC=\"$CFG->wwwroot/$ffurl\">";
                echo "</FRAMESET>";
            }
            break;

        case PLAINTEXT:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box(text_to_html($resource->alltext), "CENTER", "", "$THEME->cellcontent", "20");

            echo "<CENTER><P><FONT SIZE=1>$strlastmodified: ".userdate($resource->timemodified)."</P></CENTER>";

            print_footer($course);
            break;

        case HTML:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box_start("CENTER", "", "$THEME->cellcontent", "20");

            echo $resource->alltext;

            print_simple_box_end();

            echo "<CENTER><P><FONT SIZE=1>$strlastmodified: ".userdate($resource->timemodified)."</P></CENTER>";

            print_footer($course);
            break;

        case PROGRAM:   // Code provided by Mark Kimes <hectorp@buckfoodsvc.com>
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");

            $temptime = gmdate("YmdHis",time());

            $temproot = $CFG->wwwroot . "/mod/resource/";

            // I tried to get around this.  I really did.  But here we
            // are, redefining the navigation resources specifically anyway.
            // On the plus side, you can change the format of the navigation
            // strings above without worrying what it'll do to this code.  On
            // the negative side, you'll have to update this code if you
            // change the structure of the navigation completely.  Bonus
            // is that now we can have a chain of cooperative sites, each
            // adding to the navigation string as it moves down the line,
            // which could be quite cool.  -- Mark

            if ($course->category) {
                $tempref = "<$course->shortname><" . $temproot . "../../course/view.php?id=$course->id>" .
                           "<$strresources><" . $temproot . "index.php?id=$course->id>";
            } else {
                $tempref = "<$strresources><index.php?id=$course->id>";
            }

            $tempurl = trim($resource->reference);

            if ($tempquerystring = strstr($tempurl,'?')) {
                $tempquerystring = substr($tempquerystring,1);
                $tempurl = substr($tempurl,0,strlen($tempurl) - strlen($tempquerystring));
            }
            if (!empty($tempquerystring)) {
                $tempquerystring = preg_replace("/(.*=)([^&]*)/e", 
                                                "'\\1' . urlencode('\\2')", 
                                                $tempquerystring);
            }
            $temp = $tempurl . $tempquerystring .
                    ((strstr($tempurl,'?')) ? "&amp;" : "?") .
                    "extern_nav=" . urlencode($tempref) .
                    "&amp;extern_usr=" . 
                    urlencode($USER->username) .
                    "&amp;extern_nam=" . urlencode("$USER->firstname $USER->lastname") .
                    "&amp;extern_tim=" . urlencode($temptime) .
                    "&amp;extern_pwd=" .
                    urlencode(md5($temptime . $USER->password));
            redirect($temp);
            break;

        default:
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));
            print_heading($resource->name);

            print_simple_box("Error: unknown type of resource", "center");

            print_footer($course);
            break;
    }

?>
