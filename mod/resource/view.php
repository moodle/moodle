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
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strresources</a> ->";
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strresources</a> ->";
    }

    $pagetitle = strip_tags("$course->shortname: $resource->name");

    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation $resource->name", "", "", true, 
                     update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));
        notice(get_string("activityiscurrentlyhidden"));
    }

    switch ($resource->type) {
        case REFERENCE:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header($pagetitle, "$course->fullname", "$navigation $resource->name", "", "", true, 
                         update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box($resource->reference, "center");
            echo "<center><p>";
            echo text_to_html($resource->summary);
            echo "</p>";
            echo "<p>&nbsp</p>";
            echo "<p><font size=1>$strlastmodified: ".userdate($resource->timemodified)."</p>";
            echo "</center>";
            print_footer($course);
            break;

        case WEBLINK:
            if ($CFG->resource_filterexternalpages) {
                $url = "fetch.php?id=$cm->id&url=$resource->reference";
            } else {
                $url = "$resource->reference";
            }
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            redirect($url, "", 0);
            break;

        case WEBPAGE:
            if ($frameset == "top") {
                print_header($pagetitle, "$course->fullname", 
                  "$navigation <a target=\"{$CFG->framename}\" href=\"$resource->reference\" 
                                title=\"$resource->reference\">$resource->name</a>", "", "", true, 
                                update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm, "parent"));
                echo "<center><font size=-1>".text_to_html($resource->summary, true, false)."</font></center>";

            } else {
                if ($CFG->resource_filterexternalpages) {
                    $url = "fetch.php?id=$cm->id&url=$resource->reference";
                } else {
                    $url = "$resource->reference";
                }
                add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
                echo "<head><title>$course->shortname: $resource->name</title></head>\n";
                echo "<frameset rows=\"$CFG->resource_framesize,*\" border=\"2\">";
                echo "<frame src=\"view.php?id=$cm->id&frameset=top\">";
                echo "<frame src=\"$url\">";
                echo "</frameset>";
            }
            break;

        case UPLOADEDFILE:
            /// Possible display modes are:
            /// File displayed in a frame in a normal window
            /// File displayed embedded in a normal page
            /// File displayed in a popup window
            /// File displayed emebedded in a popup window
           

            /// First, find out what sort of file we are dealing with.
            require_once("../../files/mimetypes.php");

            $resourcetype = "";
            $embedded = false;

            $imagetypes = array('image/gif','image/jpg','image/png');
            if (in_array(mimeinfo("type", $fullurl), $imagetypes)) {  // It's an image
                $embedded = true;
                $resourcetype = "image";

            } else if (mimeinfo("icon", $fullurl) == "html.gif") {    // It's a web page
                $resourcetype = "html";
            }


            /// Set up some variables

            $inpopup = !empty($_GET["inpopup"]);

            if ($CFG->slasharguments) {
                $fullurl = "$CFG->wwwroot/file.php/$course->id/$resource->reference";
            } else {
                $fullurl = "$CFG->wwwroot/file.php?file=/$course->id/$resource->reference";
            }

            if ($CFG->resource_filterexternalpages and $resourcetype == "html") {
                $fullurl = "$CFG->wwwroot/mod/resource/fetch.php?id=$cm->id&url=$fullurl";
            }


            /// Now check whether we need to display a frameset

            if (empty($frameset) and !$embedded and !$inpopup) { 
                echo "<head><title>$course->shortname: $resource->name</title></head>\n";
                echo "<frameset rows=\"$CFG->resource_framesize,*\" border=\"2\">";
                echo "<frame src=\"view.php?id=$cm->id&frameset=top\">";
                echo "<frame src=\"$fullurl\">";
                echo "</frameset>";
                exit;
            }


            /// We can only get here once per resource, so add an entry to the log

            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");


            /// If we are in a frameset, just print the top of it

            if ($frameset == "top") { 
                print_header($pagetitle, "$course->fullname", 
                             "$navigation <a target=\"$CFG->framename\" href=\"$fullurl\">$resource->name</a>",
                             "", "", true, update_module_button($cm->id, $course->id, $strresource), 
                             navmenu($course, $cm, "parent"));
                echo "<center><font size=-1>".text_to_html($resource->summary, true, false)."</font></center>";
                echo "</body></html>";
                exit;
            }


            /// Display the actual resource

            if ($embedded) {       // Display resource embedded in page
                if ($inpopup) {
                    print_header($pagetitle);
                    echo "<center><font size=-1>".text_to_html($resource->summary, true, false)."</font></center>";
                } else {
                    print_header($pagetitle, "$course->fullname", 
                                 "$navigation <a target=\"$CFG->framename\" HREF=\"$fullurl\">$resource->name</A>",
                                 "", "", true, update_module_button($cm->id, $course->id, $strresource), 
                                 navmenu($course, $cm, "self"));
                    echo "<center><font size=-1>".text_to_html($resource->summary, true, false)."</font></center>";
                }
                if ($resourcetype == "image") {  
                    echo "<br />";
                    echo "<center><img class=\"resourceimage\" src=\"$fullurl\"></center>";
                    echo "<br />";
                }
                if (!$inpopup) {
                    print_footer($course);
                }
            } else {              // Display the resource on it's own
                redirect($fullurl);
            }
            break;


        case PLAINTEXT:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header($pagetitle, "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box(format_text($resource->alltext), "center", "", "$THEME->cellcontent", "20");

            echo "<center><p><font size=1>$strlastmodified: ".userdate($resource->timemodified)."</p></center>";

            print_footer($course);
            break;

        case HTML:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", "$resource->id");
            print_header($pagetitle, "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box_start("center", "", "$THEME->cellcontent", "20");

            echo format_text($resource->alltext, FORMAT_HTML);

            print_simple_box_end();

            echo "<center><p><font size=1>$strlastmodified: ".userdate($resource->timemodified)."</p></center>";

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
            print_header($pagetitle, "$course->fullname", "$navigation $resource->name",
                "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box(format_text($resource->alltext, FORMAT_WIKI), "center", "", "$THEME->cellcontent", "20" );

            echo "<center><p><font size=\"1\">$strlastmodified: ".userdate($resource->timemodified)."</p></center>";

            print_footer($course);
            break;
 

        default:
            print_header($pagetitle, "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));
            print_heading($resource->name);

            print_simple_box("Error: unknown type of resource", "center");

            print_footer($course);
            break;
    }

?>
