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
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", $resource->id, $cm->id);
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
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", $resource->id, $cm->id);

            $inpopup = !empty($_GET["inpopup"]);

            if ($resource->alltext and !$inpopup) {    /// Make a page and a pop-up window
                print_header($pagetitle, "$course->fullname", "$navigation $resource->name", "", "", true, 
                             update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

                echo "\n<script language=\"Javascript\">";
                echo "\n<!--\n";
                echo "openpopup('/mod/resource/view.php?inpopup=true&id=$cm->id',".
                     "'resource$resource->id','$resource->alltext');\n";
                echo "\n-->\n";
                echo '</script>';

                if (trim($resource->summary)) {
                    print_simple_box(text_to_html($resource->summary), "center");
                }

                $link = "<a href=\"$CFG->wwwroot/mod/resource/view.php?inpopup=true&id=$cm->id\" target=\"resource$resource->id\" onClick=\"return openpopup('/mod/resource/view.php?inpopup=true&id=$cm->id', 'resource$resource->id','$resource->alltext');\">$resource->name</a>";

                echo "<p>&nbsp</p>";
                echo '<p align="center">';
                print_string('popupresource', 'resource');
                echo '<br />';
                print_string('popupresourcelink', 'resource', $link);
                echo "</p>";

                print_footer($course);
                die;
            }

            if ($CFG->resource_filterexternalpages) {
                $url = "fetch.php?id=$cm->id&url=$resource->reference";
            } else {
                $url = "$resource->reference";
            }
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
                add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", $resource->id, $cm->id);
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
            $mimetype = mimeinfo("type", $resource->reference);

            if (in_array($mimetype, array('image/gif','image/jpeg','image/png'))) {  // It's an image
                $resourcetype = "image";
                $embedded = true;

            } else if ($mimetype == "audio/mp3") {    // It's an MP3 audio file
                $resourcetype = "mp3";
                $embedded = true;

            } else if (substr($mimetype, 0, 10) == "video/x-ms") {   // It's a Media Player file
                $resourcetype = "mediaplayer";
                $embedded = true;

            } else if ($mimetype == "video/quicktime") {   // It's a Quicktime file
                $resourcetype = "quicktime";
                $embedded = true;

            } else if ($mimetype == "text/html") {    // It's a web page
                $resourcetype = "html";
            }


            /// Set up some variables

            $inpopup = !empty($_GET["inpopup"]);

            if ($CFG->slasharguments) {
                $relativeurl = "/file.php/$course->id/$resource->reference";
            } else {
                $relativeurl = "/file.php?file=/$course->id/$resource->reference";
            }
            $fullurl = "$CFG->wwwroot$relativeurl";


            /// Check whether this is supposed to be a popup, but was called directly

            if ($resource->alltext and !$inpopup) {    /// Make a page and a pop-up window
                print_header($pagetitle, "$course->fullname", "$navigation $resource->name", "", "", true, 
                             update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

                echo "\n<script language=\"Javascript\">";
                echo "\n<!--\n";
                echo "openpopup('/mod/resource/view.php?inpopup=true&id=$cm->id','resource$resource->id','$resource->alltext');\n";
                echo "\n-->\n";
                echo '</script>';

                if (trim($resource->summary)) {
                    print_simple_box(text_to_html($resource->summary), "center");
                }

                $link = "<a href=\"$CFG->wwwroot/mod/resource/view.php?inpopup=true&id=$cm->id\" target=\"resource$resource->id\" onClick=\"return openpopup('/mod/resource/view.php?inpopup=true&id=$cm->id', 'resource$resource->id','$resource->alltext');\">$resource->name</a>";

                echo "<p>&nbsp</p>";
                echo '<p align="center">';
                print_string('popupresource', 'resource');
                echo '<br />';
                print_string('popupresourcelink', 'resource', $link);
                echo "</p>";

                print_footer($course);
                exit;
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

            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", $resource->id, $cm->id);


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
                $strdirectlink = get_string("directlink", "resource");

                if ($inpopup) {
                    print_header($pagetitle);
                } else {
                    print_header($pagetitle, "$course->fullname", 
                                 "$navigation <a title=\"$strdirectlink\" target=\"$CFG->framename\" ".
                                 "href=\"$fullurl\">$resource->name</a>",
                                 "", "", true, update_module_button($cm->id, $course->id, $strresource), 
                                 navmenu($course, $cm, "self"));
                }

                if ($resourcetype == "image") {  
                    echo "<center><p>";
                    echo "<img title=\"$resource->name\" class=\"resourceimage\" src=\"$fullurl\">";
                    echo "</p></center>";

                } else if ($resourcetype == "mp3") {  
                    echo "<center><p>";
                    echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
                    echo '        codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
                    echo '        width="600" height="70" id="mp3player" align="">';
                    echo "<param name=movie value=\"$CFG->wwwroot/lib/mp3player/mp3player.swf?src=$fullurl&autostart=yes\">";
                    echo '<param name=quality value=high>';
                    echo '<param name=bgcolor value="#333333">';
                    echo "<embed src=\"$CFG->wwwroot/lib/mp3player/mp3player.swf?src=$fullurl&autostart=yes\" ";
                    echo " quality=high bgcolor=\"#333333\" width=\"600\" height=\"70\" name=\"mp3player\" ";
                    echo ' type="application/x-shockwave-flash" ';
                    echo ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
                    echo '</embed>';
                    echo '</object>';
                    echo "</p></center>";

                } else if ($resourcetype == "mediaplayer") {  
                    echo "<center><p>";
                    echo '<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
                    echo '        codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
                    echo '        standby="Loading Microsoft® Windows® Media Player components..." ';
                    echo '        id="msplayer" align="" type="application/x-oleobject">';
                    echo "<param name=\"Filename\" value=\"$fullurl\">";
                    echo '<param name="ShowControls" value=true />';
                    echo '<param name="AutoRewind" value=true />';
                    echo '<param name="AutoStart" value=true />';
                    echo '<param name="Autosize" value=true />';
                    echo '<param name="EnableContextMenu" value=true />';
                    echo '<param name="TransparentAtStart" value=false />';
                    echo '<param name="AnimationAtStart" value=false />';
                    echo '<param name="ShowGotoBar" value=false />';
                    echo '<param name="EnableFullScreenControls" value=true />';
                    echo "\n<embed src=\"$fullurl\" name=\"msplayer\" type=\"$mimetype\" ";
                    echo ' ShowControls="1" AutoRewind="1" AutoStart="1" Autosize="0" EnableContextMenu="1"';
                    echo ' TransparentAtStart="0" AnimationAtStart="0" ShowGotoBar="0" EnableFullScreenControls="1"';
                    echo ' pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/">';
                    echo '</embed>';
                    echo '</object>';
                    echo "</p></center>";

                } else if ($resourcetype == "quicktime") {  

                    echo "<center><p>";
                    echo '<object classid="CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
                    echo '        codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
                    echo '        height="450" width="600"';
                    echo '        id="quicktime" align="" type="application/x-oleobject">';
                    echo "<param name=\"src\" value=\"$fullurl\" />";
                    echo '<param name="autoplay" value=true />';
                    echo '<param name="loop" value=true />';
                    echo '<param name="controller" value=true />';
                    echo '<param name="scale" value="tofit" />';
                    echo "\n<embed src=\"$fullurl\" name=\"quicktime\" type=\"$mimetype\" ";
                    echo ' height="450" width="600" scale="tofit"';
                    echo ' autoplay="true" controller="true" loop="true" ';
                    echo ' pluginspage="http://quicktime.apple.com/">';
                    echo '</embed>';
                    echo '</object>';
                    echo "</p></center>";
                }

                if (trim($resource->summary)) {
                    print_simple_box(format_text($resource->summary), 'center');
                }

                if ($inpopup) {
                    echo "<center><p>(<a href=\"$fullurl\">$strdirectlink</a>)</p></center>";
                } else {
                    print_spacer(20,20);
                    print_footer($course);
                }

            } else {              // Display the resource on it's own
                redirect($fullurl);
            }
            break;


        case PLAINTEXT:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", $resource->id, $cm->id);
            print_header($pagetitle, "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box(format_text($resource->alltext), "center", "", "$THEME->cellcontent", "20");

            echo "<center><p><font size=1>$strlastmodified: ".userdate($resource->timemodified)."</p></center>";

            print_footer($course);
            break;

        case HTML:
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", $resource->id, $cm->id);
            print_header($pagetitle, "$course->fullname", "$navigation $resource->name",
                         "", "", true, update_module_button($cm->id, $course->id, $strresource), navmenu($course, $cm));

            print_simple_box_start("center", "", "$THEME->cellcontent", "20");

            echo format_text($resource->alltext, FORMAT_HTML);

            print_simple_box_end();

            echo "<center><p><font size=1>$strlastmodified: ".userdate($resource->timemodified)."</p></center>";

            print_footer($course);
            break;

        case PROGRAM:   // Code provided by Mark Kimes <hectorp@buckfoodsvc.com>
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", $resource->id, $cm->id);

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
            add_to_log($course->id, "resource", "view", "view.php?id=$cm->id", $resource->id, $cm->id);
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
