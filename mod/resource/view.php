<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Course Module ID
    optional_variable($frameset, "");

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
        $navigation = "<A TARGET=\"{$CFG->framename}\" HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->
                       <A TARGET=\"{$CFG->framename}\" HREF=\"index.php?id=$course->id\">$strresources</A> ->";
    } else {
        $navigation = "<A TARGET=\"{$CFG->framename}\" HREF=\"index.php?id=$course->id\">$strresources</A> ->";
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
            if ($frameset == "top") {
                print_header("$course->shortname: $resource->name", "$course->fullname", 
                "$navigation <A TARGET=\"{$CFG->framename}\" HREF=\"$resource->reference\" TITLE=\"$resource->reference\">$resource->name</A>",
                "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));
                echo "<CENTER><FONT SIZE=-1>".text_to_html($resource->summary, true, false)."</FONT></CENTER>";

            } else {
                add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
                echo "<HEAD><TITLE>$course->shortname: $resource->name</TITLE></HEAD>\n";
                echo "<FRAMESET ROWS=$RESOURCE_FRAME_SIZE,*>";
                echo "<FRAME SRC=\"view.php?id=$cm->id&frameset=top\">";
                echo "<FRAME SRC=\"$resource->reference\">";
                echo "</FRAMESET>";
            }
            break;

        case UPLOADEDFILE:
            require_once("../../files/mimetypes.php");

            if ($CFG->slasharguments) {
                $fullurl = "$CFG->wwwroot/file.php/$course->id/$resource->reference";
            } else {
                $fullurl = "$CFG->wwwroot/file.php?file=/$course->id/$resource->reference";
            }


            if ($frameset == "top") {
                print_header("$course->shortname: $resource->name", "$course->fullname", 
                             "$navigation <a target=\"$CFG->framename\" HREF=\"$fullurl\">$resource->name</A>",
                             "", "", true, update_module_button($cm->id, $course->id, $strresource), 
                             navmenu($course, $cm));
                echo "<center><font size=-1>".text_to_html($resource->summary, true, false)."</font></center>";
            } else if ($frameset == "image") {
                print_header();
                echo "<center><img class=\"resourceimage\" src=\"$fullurl\"></center>";
                print_footer($course);
                
            } else {
                add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
                echo "<head><title>$course->shortname: $resource->name</title></head>\n";
                echo "<frameset rows=$RESOURCE_FRAME_SIZE,*>";
                echo "<frame src=\"view.php?id=$cm->id&frameset=top\">";
                if (mimeinfo("icon", $fullurl) == "image.gif") {
                    echo "<frame src=\"view.php?id=$cm->id&frameset=image\">";
                } else {
                    echo "<frame src=\"$fullurl\">";
                }
                echo "</frameset>";
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

        case WIKITEXT:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header("$course->shortname: $resource->name", "$course->fullname", "$navigation $resource->name",
                "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box(wiki_to_html($resource->alltext), "CENTER", "", "$THEME->cellcontent", "20" );

            echo "<center><p><font size=\"1\">$strlastmodified: ".userdate($resource->timemodified)."</p></center>";

            print_footer($course);
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
