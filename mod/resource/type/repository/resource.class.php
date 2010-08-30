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

    if (empty($USER->id)) {   // No need to set up parameters
        $this->parameters = array();
        return;
    }

    $site = get_site();

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
            'serverurl'       => array('langstr' => get_string('serverurl', 'resource', $CFG),
                                       'value'   => $CFG->wwwroot),
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
/*    echo '<xmp>';
    var_dump($_POST);
    var_dump($resource);die;*/
    return parent::update_instance($resource);
}

function _postprocess(&$resource) {
    global $RESOURCE_WINDOW_OPTIONS;
    $alloptions = $RESOURCE_WINDOW_OPTIONS;

    if ($resource->windowpopup) {
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
            $resource->options = 'frame';
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

    $formatoptions = new object();
    $formatoptions->noclean = true;

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
    $navigation = build_navigation($this->navlinks, $cm);

/// Form the parse string
    if (!empty($resource->alltext)) {
        $querys = array();
        $parray = explode(',', $resource->alltext);
        foreach ($parray as $fieldstring) {
            $field = explode('=', $fieldstring);
            $querys[] = urlencode($field[1]).'='.urlencode($this->parameters[$field[0]]['value']);
        }
        $querystring = implode('&amp;', $querys);
    }


    /// Set up some variables

    $inpopup = optional_param('inpopup', 0, PARAM_BOOL);

   $fullurl =  $resource->reference. '&amp;HIVE_SESSION='.$SESSION->HIVE_SESSION;
    if (!empty($querystring)) {
        $urlpieces = parse_url($resource->reference);
        if (empty($urlpieces['query'])) {
            $fullurl .= '?'.$querystring;
        } else {
            $fullurl .= '&amp;'.$querystring;
        }
    }

    /// MW check that the HIVE_SESSION is there
    if (empty($SESSION->HIVE_SESSION)) {
        if ($inpopup) {
            print_header($pagetitle, $course->fullname);
        } else {
            print_header($pagetitle, $course->fullname, $navigation, "", "", true,
                    update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm));
        }
        notify('You do not have access to HarvestRoad Hive. This resource is unavailable.');
        if ($inpopup) {
            close_window_button();
        }
        print_footer('none');
        die;
    }
    /// MW END

    /// Check whether this is supposed to be a popup, but was called directly

    if ($resource->popup and !$inpopup) {    /// Make a page and a pop-up window
        print_header($pagetitle, $course->fullname, $navigation, "", "", true,
                update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm));


        echo "\n<script type=\"text/javascript\">";
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

    $frameset = optional_param('frameset', '', PARAM_ALPHA);
    if (empty($frameset) and !$embedded and !$inpopup and $resource->options == "frame" and empty($USER->screenreader)) {
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
        echo "<html dir=\"ltr\">\n";
        echo '<head>';
        echo '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />';
        echo "<title>" . format_string($course->shortname) . ": ".strip_tags(format_string($resource->name,true))."</title></head>\n";
        echo "<frameset rows=\"$CFG->resource_framesize,*\">";
        echo "<frame src=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;frameset=top\" title=\"".get_string('modulename','resource')."\"/>";
        echo "<frame src=\"$fullurl\" title=\"".get_string('modulename','resource')."\"/>";
        echo "</frameset>";
        echo "</html>";
        exit;
    }


    /// We can only get here once per resource, so add an entry to the log

    add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);


    /// If we are in a frameset, just print the top of it

    if (!empty($frameset) and $frameset == "top") {
        print_header($pagetitle, $course->fullname, $navigation, "", "", true,
                update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "parent"));

        echo '<div class="summary">'.format_text($resource->summary, FORMAT_HTML, $formatoptions).'</div>';
        echo '</body></html>';
        exit;
    }

    /// Display the actual resource

    if ($embedded) {       // Display resource embedded in page
        $strdirectlink = get_string("directlink", "resource");

        if ($inpopup) {
            print_header($pagetitle);
        } else {
            print_header($pagetitle, $course->fullname, $navigation, "", "", true,
                    update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "self"));

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
            echo '        standby="Loading Microsoft� Windows� Media Player components..." ';
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

    if (!isset($defaults['popup'])) {
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
        if (array_key_exists('options', $defaults)) {
            $defaults['framepage'] = ($defaults['options']=='frame');
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

function setup_elements(&$mform) {
    global $CFG, $RESOURCE_WINDOW_OPTIONS;

    $this->set_parameters(); // set the parameter array for the form

    $mform->addElement('text', 'reference', get_string('location'), array('size'=>'48'));

    $options = 'menubar,location,toolbar,scrollbars,resizable,width=750,height=500';

    $button = $mform->addElement('button', 'browsebutton', 'Browse for content in hive...');
    $url = '/mod/resource/type/repository/hive/openlitebrowse.php';
    $buttonattributes = array('title'=>'Browse for content in hive', 'onclick'=>"return openpopup('$url', '".$button->getName()."', '$options', 0);");
    $button->updateAttributes($buttonattributes);

    $button = $mform->addElement('button', 'browsebutton', 'Search for content in Hive...');
    $url = '/mod/resource/type/repository/hive/openlitesearch.php';
    $buttonattributes = array('title'=>'Search for content in Hive', 'onclick'=>"return openpopup('$url', '".$button->getName()."', '$options', 0);");
    $button->updateAttributes($buttonattributes);

    $button = $mform->addElement('button', 'browsebutton', 'Add new item to Hive...');
    $url = '/mod/resource/type/repository/hive/openlitepublish.php';
    $buttonattributes = array('title'=>'Add new item to Hive', 'onclick'=>"return openpopup('$url', '".$button->getName()."', '$options', 0);");
    $button->updateAttributes($buttonattributes);

    $mform->addElement('header', 'displaysettings', get_string('display', 'resource'));

    $woptions = array(0 => get_string('pagewindow', 'resource'), 1 => get_string('newwindow', 'resource'));
    $mform->addElement('select', 'windowpopup', get_string('display', 'resource'), $woptions);
    $mform->setDefault('windowpopup', !empty($CFG->resource_popup));

    $mform->addElement('checkbox', 'framepage', get_string('frameifpossible', 'resource'));
    $mform->setDefault('framepage', 0);
    $mform->disabledIf('framepage', 'windowpopup', 'eq', 1);
    $mform->setAdvanced('framepage');

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
