<?php // $Id$

/**
* Extend the base resource class for file resources
*/
class resource_file extends resource_base {

    function resource_file($cmid=0) {
        parent::resource_base($cmid);
    }

    var $parameters;
    var $maxparameters = 5;


    /**
    * Sets the parameters property of the extended class
    *
    * @param    USER  global object
    * @param    CFG   global object
    */
    function set_parameters() {
        global $USER, $CFG;

        $site = get_site();

        $littlecfg = new object;       // to avoid some notices later
        $littlecfg->wwwroot = $CFG->wwwroot;


        $this->parameters = array(
                'label2'          => array('langstr' => "",
                                           'value'   =>'/optgroup'),
                'label3'          => array('langstr' => get_string('course'),
                                           'value'   => 'optgroup'),

                'courseid'        => array('langstr' => 'id',
                                           'value'   => $this->course->id),
                'coursefullname'  => array('langstr' => get_string('fullname'),
                                           'value'   => $this->course->fullname),
                'courseshortname' => array('langstr' => get_string('shortname'),
                                           'value'   => $this->course->shortname),
                'courseidnumber'  => array('langstr' => get_string('idnumbercourse'),
                                           'value'   => $this->course->idnumber),
                'coursesummary'   => array('langstr' => get_string('summary'),
                                           'value'   => $this->course->summary),
                'courseformat'    => array('langstr' => get_string('format'),
                                           'value'   => $this->course->format),
                'courseteacher'   => array('langstr' => get_string('wordforteacher'),
                                           'value'   => $this->course->teacher),
                'courseteachers'  => array('langstr' => get_string('wordforteachers'),
                                           'value'   => $this->course->teachers),
                'coursestudent'   => array('langstr' => get_string('wordforstudent'),
                                           'value'   => $this->course->student),
                'coursestudents'  => array('langstr' => get_string('wordforstudents'),
                                           'value'   => $this->course->students),

                'label4'          => array('langstr' => "",
                                           'value'   =>'/optgroup'),
                'label5'          => array('langstr' => get_string('miscellaneous'),
                                           'value'   => 'optgroup'),

                'lang'            => array('langstr' => get_string('preferredlanguage'),
                                           'value'   => current_language()),
                'sitename'        => array('langstr' => get_string('fullsitename'),
                                           'value'   => format_string($site->fullname)),
                'serverurl'       => array('langstr' => get_string('serverurl', 'resource', $littlecfg),
                                           'value'   => $littlecfg->wwwroot),
                'currenttime'     => array('langstr' => get_string('time'),
                                           'value'   => time()),
                'encryptedcode'   => array('langstr' => get_string('encryptedcode'),
                                           'value'   => $this->set_encrypted_parameter()),

                'label6'          => array('langstr' => "",
                                           'value'   =>'/optgroup')
        );

        if (!empty($USER->id)) {

            $userparameters = array(

                'label1'          => array('langstr' => get_string('user'),
                                           'value'   => 'optgroup'),

                'userid'          => array('langstr' => 'id',
                                           'value'   => $USER->id),
                'userusername'    => array('langstr' => get_string('username'),
                                           'value'   => $USER->username),
                'useridnumber'    => array('langstr' => get_string('idnumber'),
                                           'value'   => $USER->idnumber),
                'userfirstname'   => array('langstr' => get_string('firstname'),
                                           'value'   => $USER->firstname),
                'userlastname'    => array('langstr' => get_string('lastname'),
                                           'value'   => $USER->lastname),
                'userfullname'    => array('langstr' => get_string('fullname'),
                                           'value'   => fullname($USER)),
                'useremail'       => array('langstr' => get_string('email'),
                                           'value'   => $USER->email),
                'usericq'         => array('langstr' => get_string('icqnumber'),
                                           'value'   => $USER->icq),
                'userphone1'      => array('langstr' => get_string('phone').' 1',
                                           'value'   => $USER->phone1),
                'userphone2'      => array('langstr' => get_string('phone2').' 2',
                                           'value'   => $USER->phone2),
                'userinstitution' => array('langstr' => get_string('institution'),
                                           'value'   => $USER->institution),
                'userdepartment'  => array('langstr' => get_string('department'),
                                           'value'   => $USER->department),
                'useraddress'     => array('langstr' => get_string('address'),
                                           'value'   => $USER->address),
                'usercity'        => array('langstr' => get_string('city'),
                                           'value'   => $USER->city),
                'usertimezone'    => array('langstr' => get_string('timezone'),
                                           'value'   => get_user_timezone_offset()),
                'userurl'         => array('langstr' => get_string('webpage'),
                                           'value'   => $USER->url)
             );

             $this->parameters = $userparameters + $this->parameters;
        }
    }

    function add_instance($resource) {
        $this->_postprocess($resource);
        return parent::add_instance($resource);
    }


    function update_instance($resource) {
        $this->_postprocess($resource);
        return parent::update_instance($resource);
    }

    function _postprocess(&$resource) {
        global $RESOURCE_WINDOW_OPTIONS;
        $alloptions = $RESOURCE_WINDOW_OPTIONS;

        if (!empty($resource->forcedownload)) {
            $resource->popup = '';
            $resource->options = 'forcedownload';

        } else if ($resource->windowpopup) {
            $optionlist = array();
            foreach ($alloptions as $option) {
                $optionlist[] = $option."=".$resource->$option;
                unset($resource->$option);
            }
            $resource->popup = implode(',', $optionlist);
            unset($resource->windowpopup);
            $resource->options = '';

        } else {
            if (empty($resource->framepage)) {
                $resource->options = '';
            } else {
                switch ($resource->framepage) {
                    case 1:
                        $resource->options = 'frame';
                        break;
                    case 2:
                        $resource->options = 'objectframe';
                        break;
                    default:
                        $resource->options = '';
                        break;
                }
            }
            unset($resource->framepage);
            $resource->popup = '';
        }

        $optionlist = array();
        for ($i = 0; $i < $this->maxparameters; $i++) {
            $parametername = "parameter$i";
            $parsename = "parse$i";
            if (!empty($resource->$parsename) and $resource->$parametername != "-") {
                $optionlist[] = $resource->$parametername."=".$resource->$parsename;
            }
            unset($resource->$parsename);
            unset($resource->$parametername);
        }

        $resource->alltext = implode(',', $optionlist);
    }


    /**
    * Display the file resource
    *
    * Displays a file resource embedded, in a frame, or in a popup.
    * Output depends on type of file resource.
    *
    * @param    CFG     global object
    */
    function display() {
        global $CFG, $THEME, $USER;

    /// Set up generic stuff first, including checking for access
        parent::display();

    /// Set up some shorthand variables
        $cm = $this->cm;
        $course = $this->course;
        $resource = $this->resource;


        $this->set_parameters(); // set the parameters array

    ///////////////////////////////////////////////

        /// Possible display modes are:
        /// File displayed embedded in a normal page
        /// File displayed in a popup window
        /// File displayed embedded in a popup window
        /// File not displayed at all, but downloaded


        /// First, find out what sort of file we are dealing with.
        require_once($CFG->libdir.'/filelib.php');

        $querystring = '';
        $resourcetype = '';
        $embedded = false;
        $mimetype = mimeinfo("type", $resource->reference);
        $pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));

        $formatoptions = new object();
        $formatoptions->noclean = true;

        if ($resource->options != "forcedownload") { // TODO nicolasconnault 14-03-07: This option should be renamed "embed"
            if (in_array($mimetype, array('image/gif','image/jpeg','image/png'))) {  // It's an image
                $resourcetype = "image";
                    $embedded = true;

            } else if ($mimetype == "audio/mp3") {    // It's an MP3 audio file
                $resourcetype = "mp3";
                    $embedded = true;

            } else if ($mimetype == "video/x-flv") {    // It's a Flash video file
                $resourcetype = "flv";
                    $embedded = true;

            } else if (substr($mimetype, 0, 10) == "video/x-ms") {   // It's a Media Player file
                $resourcetype = "mediaplayer";
                    $embedded = true;

            } else if ($mimetype == "video/quicktime") {   // It's a Quicktime file
                $resourcetype = "quicktime";
                    $embedded = true;

            } else if ($mimetype == "application/x-shockwave-flash") {   // It's a Flash file
                $resourcetype = "flash";
                    $embedded = true;

            } else if ($mimetype == "video/mpeg") {   // It's a Mpeg file
                $resourcetype = "mpeg";
                    $embedded = true;

            } else if ($mimetype == "text/html") {    // It's a web page
                $resourcetype = "html";

            } else if ($mimetype == "application/zip") {    // It's a zip archive
                $resourcetype = "zip";
                    $embedded = true;

            } else if ($mimetype == 'application/pdf' || $mimetype == 'application/x-pdf') {
                $resourcetype = "pdf";
                //no need embedded, html file types behave like unknown file type

            } else if ($mimetype == "audio/x-pn-realaudio-plugin") {   // It's a realmedia file
                $resourcetype = "rm";
                    $embedded = true;
            }
        }

        $isteamspeak = (stripos($resource->reference, 'teamspeak://') === 0);

    /// Form the parse string
        $querys = array();
        if (!empty($resource->alltext)) {
            $parray = explode(',', $resource->alltext);
            foreach ($parray as $fieldstring) {
                list($moodleparam, $urlname) = explode('=', $fieldstring);
                $value = urlencode($this->parameters[$moodleparam]['value']);
                $querys[urlencode($urlname)] = $value;
                $querysbits[] = urlencode($urlname) . '=' . $value;
            }
            if ($isteamspeak) {
                $querystring = implode('?', $querysbits);
            } else {
                $querystring = implode('&amp;', $querysbits);
            }
        }


        /// Set up some variables

        $inpopup = optional_param('inpopup', 0, PARAM_BOOL);

        if (resource_is_url($resource->reference)) {
            $fullurl = $resource->reference;
            if (!empty($querystring)) {
                $urlpieces = parse_url($resource->reference);
                if (empty($urlpieces['query']) or $isteamspeak) {
                    $fullurl .= '?'.$querystring;
                } else {
                    $fullurl .= '&amp;'.$querystring;
                }
            }
        } else {   // Normal uploaded file
            $forcedownloadsep = '?';
            if ($resource->options == 'forcedownload') {
                $querys['forcedownload'] = '1';
            }
            $fullurl = get_file_url($course->id.'/'.$resource->reference, $querys);
        }

        /// Check whether this is supposed to be a popup, but was called directly
        if ($resource->popup and !$inpopup) {    /// Make a page and a pop-up window
            $navigation = build_navigation($this->navlinks, $cm);
            print_header($pagetitle, $course->fullname, $navigation,
                    "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm));

            echo "\n<script type=\"text/javascript\">";
            echo "\n<!--\n";
            echo "openpopup('/mod/resource/view.php?inpopup=true&id={$cm->id}','resource{$resource->id}','{$resource->popup}');\n";
            echo "\n-->\n";
            echo '</script>';

            if (trim(strip_tags($resource->summary))) {
                print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions), "center");
            }

            $link = "<a href=\"$CFG->wwwroot/mod/resource/view.php?inpopup=true&amp;id={$cm->id}\" "
                  . "onclick=\"this.target='resource{$resource->id}'; return openpopup('/mod/resource/view.php?inpopup=true&amp;id={$cm->id}', "
                  . "'resource{$resource->id}','{$resource->popup}');\">".format_string($resource->name,true)."</a>";

            echo '<div class="popupnotice">';
            print_string('popupresource', 'resource');
            echo '<br />';
            print_string('popupresourcelink', 'resource', $link);
            echo '</div>';
            print_footer($course);
            exit;
        }


        /// Now check whether we need to display a frameset

        $frameset = optional_param('frameset', '', PARAM_ALPHA);
        if (empty($frameset) and !$embedded and !$inpopup and ($resource->options == "frame" || $resource->options == "objectframe") and empty($USER->screenreader)) {
            /// display the resource into a object tag
            if ($resource->options == "objectframe") {
            /// Yahoo javascript libaries for updating embedded object size
                require_js(array('yui_utilities'));
                require_js(array('yui_container'));
                require_js(array('yui_dom-event'));
                require_js(array('yui_dom'));


            /// Moodle Header and navigation bar
                $navigation = build_navigation($this->navlinks, $cm);
                print_header($pagetitle, $course->fullname, $navigation, "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "parent"));
                $options = new object();
                $options->para = false;
                echo '</div></div>';

            /// embedded file into iframe if the resource is on another domain
            ///
            /// This case is not XHTML strict but there is no alternative
            /// The object tag alternative is XHTML strict, however IE6-7 displays a blank object on accross domain by default,
            /// so we decided to use iframe for accross domain MDL-10021
                if (!stristr($fullurl,$CFG->wwwroot)) {
                    echo '<p><iframe id="embeddedhtml" src ="'.$fullurl.'" width="100%" height="600"></iframe></p>';
                }
                else {
                /// embedded HTML file into an object tag
                    echo '<p><object id="embeddedhtml" data="' . $fullurl . '" type="'.$mimetype.'" width="800" height="600">
                            alt : <a href="' . $fullurl . '">' . $fullurl . '</a>
                          </object></p>';
                }
            /// add some javascript in order to fit this object tag into the browser window
                echo '<script type="text/javascript">
                             //<![CDATA[
                             function resizeEmbeddedHtml() {
                             //calculate new embedded html height size
                     ';
                if (!empty($resource->summary)) {
                    echo'    objectheight =  YAHOO.util.Dom.getViewportHeight() - 230;
                        ';
                }
                else {
                     echo'    objectheight =  YAHOO.util.Dom.getViewportHeight() - 120;
                         ';
                }
                echo '       //the object tag cannot be smaller than a human readable size
                             if (objectheight < 200) {
                                 objectheight = 200;
                             }
                             //resize the embedded html object
                             YAHOO.util.Dom.setStyle("embeddedhtml", "height", objectheight+"px");
                             YAHOO.util.Dom.setStyle("embeddedhtml", "width", "100%");
                          }
                          resizeEmbeddedHtml();
                          YAHOO.widget.Overlay.windowResizeEvent.subscribe(resizeEmbeddedHtml);
                          //]]>
                       </script>
                    ';

            /// print the summary
                if (!empty($resource->summary)) {
                    print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center");
                }
                echo "</body></html>";
                exit;
            }
            /// display the resource into a frame tag
            else {
                @header('Content-Type: text/html; charset=utf-8');
                echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
                echo "<html dir=\"ltr\">\n";
                echo '<head>';
                echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
                echo "<title>" . format_string($course->shortname) . ": ".strip_tags(format_string($resource->name,true))."</title></head>\n";
                echo "<frameset rows=\"$CFG->resource_framesize,*\">";
                echo "<frame src=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;frameset=top\" title=\"".get_string('modulename','resource')."\"/>";
                echo "<frame src=\"$fullurl\" title=\"".get_string('modulename','resource')."\"/>";
                echo "</frameset>";
                echo "</html>";
                exit;
            }
        }


        /// We can only get here once per resource, so add an entry to the log

        add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);


        /// If we are in a frameset, just print the top of it

        if (!empty( $frameset ) and ($frameset == "top") ) {

            // Force target atttributes on links.  Not Strict but we are already using frames anyway! MDL-20327
            $CFG->frametarget = ' target="'.$CFG->framename.'" ';

            $navigation = build_navigation($this->navlinks, $cm);
            print_header($pagetitle, $course->fullname, $navigation,
                    "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "parent"));

            $options = new object();
            $options->para = false;
            echo '<div class="summary">'.format_text($resource->summary, FORMAT_HTML, $options).'</div>';
            print_footer('empty');
            exit;
        }


        /// Display the actual resource
        if ($embedded) {       // Display resource embedded in page
            $strdirectlink = get_string("directlink", "resource");

            if ($inpopup) {
                print_header($pagetitle);
            } else {
            $navigation = build_navigation($this->navlinks, $cm);
            print_header_simple($pagetitle, '', $navigation, "", "", true,
            update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "self"));

            }

            if ($resourcetype == "image") {
                echo '<div class="resourcecontent resourceimg">';
                echo "<img title=\"".strip_tags(format_string($resource->name,true))."\" class=\"resourceimage\" src=\"$fullurl\" alt=\"\" />";
                echo '</div>';

            } else if ($resourcetype == "mp3") {
                if (!empty($THEME->resource_mp3player_colors)) {
                    $c = $THEME->resource_mp3player_colors;   // You can set this up in your theme/xxx/config.php
                } else {
                    $c = 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
                         'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
                         'font=Arial&fontColour=FF33FF&buffer=10&waitForPlay=no&autoPlay=yes';
                }
                $c .= '&volText='.get_string('vol', 'resource').'&panText='.get_string('pan','resource');
                $c = htmlentities($c);
                $id = 'filter_mp3_'.time(); //we need something unique because it might be stored in text cache
                $cleanurl = addslashes_js($fullurl);


                // If we have Javascript, use UFO to embed the MP3 player, otherwise depend on plugins

                echo '<div class="resourcecontent resourcemp3">';

                echo '<span class="mediaplugin mediaplugin_mp3" id="'.$id.'"></span>'.
                     '<script type="text/javascript">'."\n".
                     '//<![CDATA['."\n".
                       'var FO = { movie:"'.$CFG->wwwroot.'/lib/mp3player/mp3player.swf?src='.$cleanurl.'",'."\n".
                         'width:"600", height:"70", majorversion:"6", build:"40", flashvars:"'.$c.'", quality: "high" };'."\n".
                       'UFO.create(FO, "'.$id.'");'."\n".
                     '//]]>'."\n".
                     '</script>'."\n";

                echo '<noscript>';

                echo "<object type=\"audio/mpeg\" data=\"$fullurl\" width=\"600\" height=\"70\">";
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo '<param name="quality" value="high" />';
                echo '<param name="autoplay" value="true" />';
                echo '<param name="autostart" value="true" />';
                echo '</object>';
                echo '<p><a href="' . $fullurl . '">' . $fullurl . '</a></p>';

                echo '</noscript>';
                echo '</div>';

            } else if ($resourcetype == "flv") {
                $id = 'filter_flv_'.time(); //we need something unique because it might be stored in text cache
                $cleanurl = addslashes_js($fullurl);


                // If we have Javascript, use UFO to embed the FLV player, otherwise depend on plugins

                echo '<div class="resourcecontent resourceflv">';

                echo '<span class="mediaplugin mediaplugin_flv" id="'.$id.'"></span>'.
                     '<script type="text/javascript">'."\n".
                     '//<![CDATA['."\n".
                       'var FO = { movie:"'.$CFG->wwwroot.'/filter/mediaplugin/flvplayer.swf?file='.$cleanurl.'",'."\n".
                         'width:"600", height:"400", majorversion:"6", build:"40", allowscriptaccess:"never", allowfullscreen:"true", quality: "high" };'."\n".
                       'UFO.create(FO, "'.$id.'");'."\n".
                     '//]]>'."\n".
                     '</script>'."\n";

                echo '<noscript>';

                echo "<object type=\"video/x-flv\" data=\"$fullurl\" width=\"600\" height=\"400\">";
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo '<param name="quality" value="high" />';
                echo '<param name="autoplay" value="true" />';
                echo '<param name="autostart" value="true" />';
                echo '</object>';
                echo '<p><a href="' . $fullurl . '">' . $fullurl . '</a></p>';

                echo '</noscript>';
                echo '</div>';

            } else if ($resourcetype == "mediaplayer") {
                echo '<div class="resourcecontent resourcewmv">';
                echo '<object type="video/x-ms-wmv" data="' . $fullurl . '">';
                echo '<param name="controller" value="true" />';
                echo '<param name="autostart" value="true" />';
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo '<param name="scale" value="noScale" />';
                echo "<a href=\"$fullurl\">$fullurl</a>";
                echo '</object>';
                echo '</div>';

            } else if ($resourcetype == "mpeg") {
                echo '<div class="resourcecontent resourcempeg">';
                echo '<object classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95"
                              codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsm p2inf.cab#Version=5,1,52,701"
                              type="application/x-oleobject">';
                echo "<param name=\"fileName\" value=\"$fullurl\" />";
                echo '<param name="autoStart" value="true" />';
                echo '<param name="animationatStart" value="true" />';
                echo '<param name="transparentatStart" value="true" />';
                echo '<param name="showControls" value="true" />';
                echo '<param name="Volume" value="-450" />';
                echo '<!--[if !IE]>-->';
                echo '<object type="video/mpeg" data="' . $fullurl . '">';
                echo '<param name="controller" value="true" />';
                echo '<param name="autostart" value="true" />';
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo "<a href=\"$fullurl\">$fullurl</a>";
                echo '<!--<![endif]-->';
                echo '<a href="' . $fullurl . '">' . $fullurl . '</a>';
                echo '<!--[if !IE]>-->';
                echo '</object>';
                echo '<!--<![endif]-->';
                echo '</object>';
                echo '</div>';
            } else if ($resourcetype == "rm") {

                echo '<div class="resourcecontent resourcerm">';
                echo '<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="320" height="240">';
                echo '<param name="src" value="' . $fullurl . '" />';
                echo '<param name="controls" value="All" />';
                echo '<!--[if !IE]>-->';
                echo '<object type="audio/x-pn-realaudio-plugin" data="' . $fullurl . '" width="320" height="240">';
                echo '<param name="controls" value="All" />';
                echo '<a href="' . $fullurl . '">' . $fullurl .'</a>';
                echo '</object>';
                echo '<!--<![endif]-->';
                echo '</object>';
                echo '</div>';

            } else if ($resourcetype == "quicktime") {
                echo '<div class="resourcecontent resourceqt">';

                echo '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
                echo '        codebase="http://www.apple.com/qtactivex/qtplugin.cab">';
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo '<param name="autoplay" value="true" />';
                echo '<param name="loop" value="true" />';
                echo '<param name="controller" value="true" />';
                echo '<param name="scale" value="aspect" />';

                echo '<!--[if !IE]>-->';
                echo "<object type=\"video/quicktime\" data=\"$fullurl\">";
                echo '<param name="controller" value="true" />';
                echo '<param name="autoplay" value="true" />';
                echo '<param name="loop" value="true" />';
                echo '<param name="scale" value="aspect" />';
                echo '<!--<![endif]-->';
                echo '<a href="' . $fullurl . '">' . $fullurl . '</a>';
                echo '<!--[if !IE]>-->';
                echo '</object>';
                echo '<!--<![endif]-->';
                echo '</object>';
                echo '</div>';
            }  else if ($resourcetype == "flash") {
                echo '<div class="resourcecontent resourceswf">';
                echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">';
                echo "<param name=\"movie\" value=\"$fullurl\" />";
                echo '<param name="autoplay" value="true" />';
                echo '<param name="loop" value="true" />';
                echo '<param name="controller" value="true" />';
                echo '<param name="scale" value="aspect" />';
                echo '<param name="base" value="." />';
                echo '<!--[if !IE]>-->';
                echo "<object type=\"application/x-shockwave-flash\" data=\"$fullurl\">";
                echo '<param name="controller" value="true" />';
                echo '<param name="autoplay" value="true" />';
                echo '<param name="loop" value="true" />';
                echo '<param name="scale" value="aspect" />';
                echo '<param name="base" value="." />';
                echo '<!--<![endif]-->';
                echo '<a href="' . $fullurl . '">' . $fullurl . '</a>';
                echo '<!--[if !IE]>-->';
                echo '</object>';
                echo '<!--<![endif]-->';
                echo '</object>';
                echo '</div>';

            } elseif ($resourcetype == 'zip') {
                echo '<div class="resourcepdf">';
                echo get_string('clicktoopen', 'resource') . '<a href="' . $fullurl . '">' . format_string($resource->name) . '</a>';
                echo '</div>';
            }

            if (trim($resource->summary)) {
                print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center");
            }

            if ($inpopup) {
                echo "<div class=\"popupnotice\">(<a href=\"$fullurl\">$strdirectlink</a>)</div>";
                print_footer($course); // MDL-12098
            } else {
                print_spacer(20,20);
                print_footer($course);
            }

        } else {              // Display the resource on it's own
            redirect($fullurl);
        }

    }


    //backwards compatible with existing resources
    function set_encrypted_parameter() {
        global $CFG;

        if (!empty($this->resource->reference) && file_exists($CFG->dirroot ."/mod/resource/type/file/externserverfile.php")) {
            include $CFG->dirroot ."/mod/resource/type/file/externserverfile.php";
            if (function_exists('extern_server_file')) {
                return extern_server_file($this->resource->reference);
            }
        }
        return md5(getremoteaddr().$CFG->resource_secretphrase);
    }

    function setup_preprocessing(&$defaults){

        if (isset($defaults['options']) and $defaults['options'] === 'forcedownload') {
            $defaults['forcedownload'] = 1;

        } else if (!isset($defaults['popup'])) {
            // use form defaults

        } else if (!empty($defaults['popup'])) {
            $defaults['windowpopup'] = 1;
            if (array_key_exists('popup', $defaults)) {
                $rawoptions = explode(',', $defaults['popup']);
                foreach ($rawoptions as $rawoption) {
                    $option = explode('=', trim($rawoption));
                    $defaults[$option[0]] = $option[1];
                }
            }
        } else {
            $defaults['windowpopup'] = 0;
        ///set default value of 'keep navigation visible'
            if (array_key_exists('options', $defaults)) {
                if ($defaults['options']=='frame') {
                    $defaults['framepage'] = 1;
                } else if ($defaults['options']=='objectframe') {
                    $defaults['framepage'] = 2;
                } else {
                    $defaults['framepage'] = 0;
                }
            }
        }
        /// load up any stored parameters
        if (!empty($defaults['alltext'])) {
            $parray = explode(',', $defaults['alltext']);
            $i=0;
            foreach ($parray as $rawpar) {
                list($param, $varname) = explode('=', $rawpar);
                $defaults["parse$i"] = $varname;
                $defaults["parameter$i"] = $param;
                $i++;
            }
        }
    }

    /**
     * TODO document
     */
    function setup_elements(&$mform) {
        global $CFG, $RESOURCE_WINDOW_OPTIONS;

        $this->set_parameters(); // set the parameter array for the form

        $mform->addElement('choosecoursefile', 'reference', get_string('location'), null, array('maxlength' => 255, 'size' => 48));
        $mform->setDefault('reference', $CFG->resource_defaulturl);
        $referencegrprules = array();
        $referencegrprules['value'][] = array(get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $referencegrprules['value'][] = array(null, 'required', null, 'client');
        $mform->addGroupRule('reference', $referencegrprules);
        $mform->addRule('reference', null, 'required', null, 'client');

        if (!empty($CFG->resource_websearch)) {
            $searchbutton = $mform->addElement('button', 'searchbutton', get_string('searchweb', 'resource').'...');
            $buttonattributes = array('title'=>get_string('searchweb', 'resource'), 'onclick'=>"return window.open('"
                              . "$CFG->resource_websearch', 'websearch', 'menubar=1,location=1,directories=1,toolbar=1,"
                              . "scrollbars,resizable,width=800,height=600');");
            $searchbutton->updateAttributes($buttonattributes);
        }

        $mform->addElement('header', 'displaysettings', get_string('display', 'resource'));

        $mform->addElement('checkbox', 'forcedownload', get_string('forcedownload', 'resource'));
        $mform->setHelpButton('forcedownload', array('forcedownload', get_string('forcedownload', 'resource'), 'resource'));
        $mform->disabledIf('forcedownload', 'windowpopup', 'eq', 1);

        $woptions = array(0 => get_string('pagewindow', 'resource'), 1 => get_string('newwindow', 'resource'));
        $mform->addElement('select', 'windowpopup', get_string('display', 'resource'), $woptions);
        $mform->disabledIf('windowpopup', 'forcedownload', 'checked');
        $mform->setDefault('windowpopup', !empty($CFG->resource_popup));

        $navoptions = array(0 => get_string('keepnavigationvisibleno','resource'), 1 => get_string('keepnavigationvisibleyesframe','resource'), 2 => get_string('keepnavigationvisibleyesobject','resource'));
        $mform->addElement('select', 'framepage', get_string('keepnavigationvisible', 'resource'), $navoptions);

        $mform->setHelpButton('framepage', array('frameifpossible', get_string('keepnavigationvisible', 'resource'), 'resource'));
        $mform->setDefault('framepage', 0);
        $mform->disabledIf('framepage', 'windowpopup', 'eq', 1);
        $mform->disabledIf('framepage', 'forcedownload', 'checked');
        $mform->setAdvanced('framepage');

        $mform->addElement('static','shownavigationwarning','','<i>'.get_string('keepnavigationvisiblewarning', 'resource').'</i>');

        foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
            if ($option == 'height' or $option == 'width') {
                $mform->addElement('text', $option, get_string('new'.$option, 'resource'), array('size'=>'4'));
                $mform->setDefault($option, $CFG->{'resource_popup'.$option});
                $mform->disabledIf($option, 'windowpopup', 'eq', 0);
            } else {
                $mform->addElement('checkbox', $option, get_string('new'.$option, 'resource'));
                $mform->setDefault($option, $CFG->{'resource_popup'.$option});
                $mform->disabledIf($option, 'windowpopup', 'eq', 0);
            }
            $mform->setAdvanced($option);
        }

        $mform->addElement('header', 'parameters', get_string('parameters', 'resource'));

        $options = array();
        $options['-'] = get_string('chooseparameter', 'resource').'...';
        $optgroup = '';
        foreach ($this->parameters as $pname=>$param) {
            if ($param['value']=='/optgroup') {
                $optgroup = '';
                continue;
            }
            if ($param['value']=='optgroup') {
                $optgroup = $param['langstr'];
                continue;
            }
            $options[$pname] = $optgroup.' - '.$param['langstr'];
        }

        for ($i = 0; $i < $this->maxparameters; $i++) {
            $parametername = "parameter$i";
            $parsename = "parse$i";
            $group = array();
            $group[] =& $mform->createElement('text', $parsename, '', array('size'=>'12'));//TODO: accessiblity
            $group[] =& $mform->createElement('select', $parametername, '', $options);//TODO: accessiblity
            $mform->addGroup($group, 'pargroup'.$i, get_string('variablename', 'resource').'='.get_string('parameter', 'resource'), ' ', false);
            $mform->setAdvanced('pargroup'.$i);

            $mform->setDefault($parametername, '-');
        }
    }

}

?>
