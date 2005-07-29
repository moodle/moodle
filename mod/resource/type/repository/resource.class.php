<?php // $Id$

/**
* Extend the base resource class for repository resources
*
* Extend the base resource class for respoitory resources
*
*/
class resource_repository extends resource_base {

function resource_repository($cmid=0) {
    parent::resource_base($cmid);
}

var $parameters;
var $maxparameters = 5;


/**
* Sets the parameters property of the extended class
*
* Sets the parameters property of the extended repository resource class
*
* @param    USER  global object
* @param    CFG   global object
*/
function set_parameters() {
    global $USER, $CFG;

    if (! empty($this->course->lang)) {
        $CFG->courselang = $this->course->lang;
    }

    if (empty($USER->id)) {   // No need to set up parameters
        $this->parameters = array();
        return;
    }

    $site = get_site();

    $this->parameters = array(

            'label1'          => array('langstr' => get_string('user'),
                                       'value'   => 'optgroup'),

            'userid'          => array('langstr' => 'id',
                                       'value'   => $USER->id),
            'userusername'    => array('langstr' => get_string('username'),
                                       'value'   => $USER->username),
            'userpassword'    => array('langstr' => get_string('password'),
                                       'value'   => $USER->password),
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
            'userphone2'      => array('langstr' => get_string('phone').' 2',
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
                                       'value'   => $USER->url),

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
            'courseidnumber'  => array('langstr' => get_string('idnumber'),
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
                                       'value'   => $site->fullname),
            'serverurl'       => array('langstr' => get_string('serverurl', 'resource', $CFG),
                                       'value'   => $CFG->wwwroot),
            'currenttime'     => array('langstr' => get_string('time'),
                                       'value'   => time()),
            'encryptedcode'   => array('langstr' => get_string('encryptedcode'),
                                       'value'   => $this->set_encrypted_parameter()),

            'label6'          => array('langstr' => "",
                                       'value'   =>'/optgroup')
            );

}


/**
* Add new instance of repository resource
*
* Create alltext field before calling base class function.
*
* @param    resource object
*/
function add_instance($resource) {
    $optionlist = array();

    for ($i = 0; $i < $this->maxparameters; $i++) {
        $parametername = "parameter$i";
        $parsename = "parse$i";
        if (!empty($resource->$parsename) and $resource->$parametername != "-") {
            $optionlist[] = $resource->$parametername."=".$resource->$parsename;
        }
    }

    $resource->alltext = implode(',', $optionlist);

    return parent::add_instance($resource);
}


/**
* Update instance of repository resource
*
* Create alltext field before calling base class function.
*
* @param    resource object
*/
function update_instance($resource) {
    $optionlist = array();

    for ($i = 0; $i < $this->maxparameters; $i++) {
        $parametername = "parameter$i";
        $parsename = "parse$i";
        if (!empty($resource->$parsename) and $resource->$parametername != "-") {
            $optionlist[] = $resource->$parametername."=".$resource->$parsename;
        }
    }

    $resource->alltext = implode(',', $optionlist);

    return parent::update_instance($resource);
}


/**
* Display the repository resource
*
* Displays a repository resource embedded, in a frame, or in a popup.
* Output depends on type of file resource.
*
* @param    CFG     global object
*/
function display() {
    global $CFG, $THEME, $SESSION;

/// Set up generic stuff first, including checking for access
    parent::display();

/// Set up some shorthand variables
    $cm = $this->cm;
    $course = $this->course;
    $resource = $this->resource;


    $this->set_parameters(); // set the parameters array

///////////////////////////////////////////////

    /// Possible display modes are:
    /// File displayed in a frame in a normal window
    /// File displayed embedded in a normal page
    /// File displayed in a popup window
    /// File displayed emebedded in a popup window


    /// First, find out what sort of file we are dealing with.
    require_once($CFG->libdir.'/filelib.php');

    $querystring = '';
    $resourcetype = '';
    $embedded = false;
    $mimetype = mimeinfo("type", $resource->reference);
    $pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));

    if ($resource->options != "frame") {
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
    }



/// Form the parse string
    if (!empty($resource->alltext)) {
        $querys = array();
        $parray = explode(',', $resource->alltext);
        foreach ($parray as $fieldstring) {
            $field = explode('=', $fieldstring);
            $querys[] = urlencode($field[1]).'='.urlencode($this->parameters[$field[0]]['value']);
        }
        $querystring = implode('&', $querys);
    }


    /// Set up some variables

    $inpopup = !empty($_GET["inpopup"]);
    

    $fullurl =  $CFG->hiveprotocol .'://'. $CFG->hivehost .':'. $CFG->hiveport .''. $CFG->hivepath . '?'. $resource->reference . '&amp;HIVE_SESSION='.$SESSION->HIVE_SESSION;

    if (!empty($querystring)) {
        $urlpieces = parse_url($resource->reference);
        if (empty($urlpieces['query'])) {
            $fullurl .= '?'.$querystring;
        } else {
            $fullurl .= '&'.$querystring;
        }
    }

    /// MW check that the HIVE_SESSION is there
    if (empty($SESSION->HIVE_SESSION)) {
        if ($inpopup) {
            print_header($pagetitle, $course->fullname);
        } else {
            print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name), "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm));
        }
        notify('You do not have access to HarvestRoad Hive. This resource is unavailable.');
        if ($inpopup) {
            close_window_button();
        }
        print_footer('none');
        die;
    }
    /// MW END


    /// Print a notice and redirect if we are trying to access a file on a local file system
    /// and the config setting has been disabled
    if (!$CFG->resource_allowlocalfiles and (strpos($resource->reference, RESOURCE_LOCALPATH) === 0)) {
        if ($inpopup) {
            print_header($pagetitle, $course->fullname);
        } else {
            print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name), "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm));
        }
        notify(get_string('notallowedlocalfileaccess', 'resource', ''));
        if ($inpopup) {
            close_window_button();
        }
        print_footer('none');
        die;
    }


    /// Check whether this is supposed to be a popup, but was called directly

    if ($resource->popup and !$inpopup) {    /// Make a page and a pop-up window

        print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name), "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm));


        echo "\n<script language=\"javascript\" type=\"text/javascript\">";
        echo "\n<!--\n";
        echo "openpopup('/mod/resource/view.php?inpopup=true&id={$cm->id}','resource{$resource->id}','{$resource->popup}');\n";
        echo "\n-->\n";
        echo '</script>';

        if (trim(strip_tags($resource->summary))) {
            $formatoptions->noclean = true;
            print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions), "center");
        }

        $link = "<a href=\"$CFG->wwwroot/mod/resource/view.php?inpopup=true&amp;id={$cm->id}\" target=\"resource{$resource->id}\" onclick=\"return openpopup('/mod/resource/view.php?inpopup=true&amp;id={$cm->id}', 'resource{$resource->id}','{$resource->popup}');\">".format_string($resource->name,true)."</a>";

        echo "<p>&nbsp;</p>";
        echo '<p align="center">';
        print_string('popupresource', 'resource');
        echo '<br />';
        print_string('popupresourcelink', 'resource', $link);
        echo "</p>";

        print_footer($course);
        exit;
    }


    /// Now check whether we need to display a frameset

    if (empty($_GET['frameset']) and !$embedded and !$inpopup and $resource->options == "frame") {
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
        echo "<html dir=\"ltr\">\n";
        echo '<head>';
        echo '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />';
        echo "<title>{$course->shortname}: ".strip_tags(format_string($resource->name,true))."</title></head>\n";
        echo "<frameset rows=\"$CFG->resource_framesize,*\">";
        echo "<frame src=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;frameset=top\" />";
        if (!empty($localpath)) {  // Show it like this so we interpose some HTML
            echo "<frame src=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;inpopup=true\" />";
        } else {
            echo "<frame src=\"$fullurl\" />";
        }
        echo "</frameset>";
        echo "</html>";
        exit;
    }


    /// We can only get here once per resource, so add an entry to the log

    add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);


    /// If we are in a frameset, just print the top of it

    if (!empty($_GET['frameset']) and $_GET['frameset'] == "top") {
        print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name), "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "parent"));

        $options->para = false;
        echo '<div class="summary">'.format_text($resource->summary, FORMAT_HTML, $options).'</div>';
        if (!empty($localpath)) {  // Show some help
            echo '<div align="right" class="helplink">';
            link_to_popup_window ('/mod/resource/type/file/localpath.php', get_string('localfile', 'resource'), get_string('localfilehelp','resource'), 400, 500, get_string('localfilehelp', 'resource'));
            echo '</div>';
        }
        echo '</body></html>';
        exit;
    }


    /// Display the actual resource

    if ($embedded) {       // Display resource embedded in page
        $strdirectlink = get_string("directlink", "resource");

        if ($inpopup) {
            print_header($pagetitle);
        } else {
            print_header($pagetitle, $course->fullname, "$this->navigation <a title=\"$strdirectlink\" target=\"$CFG->framename\" href=\"$fullurl\"> ".format_string($resource->name,true)."</a>", "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "self"));

        }

        if ($resourcetype == "image") {
            echo "<center><p>";
            echo "<img title=\"".strip_tags(format_string($resource->name,true))."\" class=\"resourceimage\" src=\"$fullurl\" alt=\"\" />";
            echo "</p></center>";

        } else if ($resourcetype == "mp3") {
            if (!empty($THEME->resource_mp3player_colors)) {
                $c = $THEME->resource_mp3player_colors;   // You can set this up in your theme/xxx/config.php
            } else {
                $c = 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
                     'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
                     'font=Arial&fontColour=3333FF&buffer=10&waitForPlay=no&autoPlay=yes';
            }
            $c .= '&volText='.get_string('vol', 'resource').'&panText='.get_string('pan','resource');
            $c = htmlentities($c);
            echo '<div class="mp3player" align="center">';
            echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
            echo '        codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
            echo '        width="600" height="70" id="mp3player" align="">';
            echo '<param name="movie" value="'.$CFG->wwwroot.'/lib/mp3player/mp3player.swf?src='.$fullurl.'">';
            echo '<param name="quality" value="high">';
            echo '<param name="bgcolor" value="#333333">';
            echo '<param name="flashvars" value="'.$c.'&amp;" />';
            echo '<embed src="'.$CFG->wwwroot.'/lib/mp3player/mp3player.swf?src='.$fullurl.'" ';
            echo ' quality="high" bgcolor="#333333" width="600" height="70" name="mp3player" ';
            echo ' type="application/x-shockwave-flash" ';
            echo ' flashvars="'.$c.'&amp;" ';
            echo ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
            echo '</embed>';
            echo '</object>';
            echo '</div>';


        } else if ($resourcetype == "mediaplayer") {
            echo "<center><p>";
            echo '<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
            echo '        codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
            echo '        standby="Loading Microsoft® Windows® Media Player components..." ';
            echo '        id="msplayer" align="" type="application/x-oleobject">';
            echo "<param name=\"Filename\" value=\"$fullurl\">";
            echo '<param name="ShowControls" value="true" />';
            echo '<param name="AutoRewind" value="true" />';
            echo '<param name="AutoStart" value="true" />';
            echo '<param name="Autosize" value="true" />';
            echo '<param name="EnableContextMenu" value="true" />';
            echo '<param name="TransparentAtStart" value="false" />';
            echo '<param name="AnimationAtStart" value="false" />';
            echo '<param name="ShowGotoBar" value="false" />';
            echo '<param name="EnableFullScreenControls" value="true" />';
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
            echo '<param name="autoplay" value="true" />';
            echo '<param name="loop" value="true" />';
            echo '<param name="controller" value="true" />';
            echo '<param name="scale" value="aspect" />';
            echo "\n<embed src=\"$fullurl\" name=\"quicktime\" type=\"$mimetype\" ";
            echo ' height="450" width="600" scale="aspect"';
            echo ' autoplay="true" controller="true" loop="true" ';
            echo ' pluginspage="http://quicktime.apple.com/">';
            echo '</embed>';
            echo '</object>';
            echo "</p></center>";
        }

        if (trim($resource->summary)) {
            $formatoptions->noclean = true;
            print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center");
        }

        if ($inpopup) {
            echo "<center><p>(<a href=\"$fullurl\">$strdirectlink</a>)</p></center>";
        } else {
            print_spacer(20,20);
            print_footer($course);
        }

    } else {              // Display the resource on it's own
        if (!empty($localpath)) {   // Show a link to help work around browser security
            echo '<div align="right" class="helplink">';
            link_to_popup_window ('/mod/resource/type/file/localpath.php', get_string('localfile', 'resource'), get_string('localfilehelp','resource'), 400, 500, get_string('localfilehelp', 'resource'));
            echo '</div>';
            echo "<center><p>(<a href=\"$fullurl\">$fullurl</a>)</p></center>";
        }
        redirect($fullurl);
    }

}



/**
* Setup a new repository resource
*
* Display a form to create a new or edit an existing repository resource
*
* @param    form                    object
* @param    CFG                     global object
* @param    usehtmleditor           global integer
* @param    RESOURCE_WINDOW_OPTIONS global array
*/
function setup($form) {
    global $CFG, $usehtmleditor, $RESOURCE_WINDOW_OPTIONS;

    parent::setup($form);

    $this->set_parameters(); // set the parameter array for the form


    $strfilename = get_string("location");
    $strnote     = get_string("note", "resource");
    $strchooseafile = get_string("chooseafile", "resource");
    $strnewwindow     = get_string("newwindow", "resource");
    $strnewwindowopen = get_string("newwindowopen", "resource");
    $strsearch        = get_string("searchweb", "resource");

    foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
        $stringname = "str$optionname";
        $$stringname = get_string("new$optionname", "resource");
        $window->$optionname = "";
        $jsoption[] = "\"$optionname\"";
    }

    $frameoption = "\"framepage\"";
    $popupoptions = implode(",", $jsoption);
    $jsoption[] = $frameoption;
    $alloptions = implode(",", $jsoption);



    if ($form->instance) {     // Re-editing
        if (!$form->popup) {
            $windowtype = "page";   // No popup text => in page
            foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
                $defaultvalue = "resource_popup$optionname";
                $window->$optionname = $CFG->$defaultvalue;
            }
        } else {
            $windowtype = "popup";
            $rawoptions = explode(',', $form->popup);
            foreach ($rawoptions as $rawoption) {
                $option = explode('=', trim($rawoption));
                $optionname = $option[0];
                $optionvalue = $option[1];
                if ($optionname == 'height' or $optionname == 'width') {
                    $window->$optionname = $optionvalue;
                } else if ($optionvalue) {
                    $window->$optionname = 'checked="checked"';
                }
            }
        }
    } else {
        foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
            $defaultvalue = "resource_popup$optionname";

            if ($optionname == 'height' or $optionname == 'width') {
                $window->$optionname = $CFG->$defaultvalue;
            } else if ($CFG->$defaultvalue) {
                $window->$optionname = 'checked="checked"';
            }
        }

        $windowtype = ($CFG->resource_popup) ? 'popup' : 'page';
        if (empty($form->options)) {
            $form->options = 'frame';
            $form->reference = $CFG->resource_defaulturl;
        }
    }
    if (empty($form->reference)) {
        $form->reference = $CFG->resource_defaulturl;
    }


/// set the 5 parameter defaults
    $alltextfield = array();
    for ($i = 0; $i < $this->maxparameters; $i++) {
        $alltextfield[] = array('parameter' => '',
                                'parse'     => '');
    }
    /// load up any stored parameters
    if (!empty($form->alltext)) {
        $parray = explode(',', $form->alltext);
        foreach ($parray as $key => $fieldstring) {
            $field = explode('=', $fieldstring);
            $alltextfield[$key]['parameter'] = $field[0];
            $alltextfield[$key]['parse'] = $field[1];
        }
    }


    include("$CFG->dirroot/mod/resource/type/repository/repository.html");

    parent::setup_end();
}

//backwards compatible with existing resources
function set_encrypted_parameter() {
    global $CFG;

    if (!empty($this->resource->reference) && file_exists($CFG->dirroot ."/mod/resource//type/file/externserverfile.php")) {
        include $CFG->dirroot ."/mod/resource/type/file/externserverfile.php";
        if (function_exists(extern_server_file)) {
            return extern_server_file($this->resource->reference);
        }
    }
    return md5($_SERVER['REMOTE_ADDR'].$CFG->resource_secretphrase);
}

}

?>
