<?php // $Id$

class resource_html extends resource_base {


function resource_html($cmid=0) {
    parent::resource_base($cmid);
}

function add_instance($resource) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    global $RESOURCE_WINDOW_OPTIONS;

    $resource->timemodified = time();

    if (isset($resource->windowpopup)) {
        $optionlist = array();
        foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
            if (isset($resource->$option)) {
                $optionlist[] = $option."=".$resource->$option;
            }
        }
        $resource->popup = implode(',', $optionlist);

    } else if (isset($resource->windowpage)) {
        $resource->popup = "";
    }

    if (isset($resource->parametersettingspref)) {
        set_user_preference('resource_parametersettingspref', $resource->parametersettingspref);
    }
    if (isset($resource->windowsettingspref)) {
        set_user_preference('resource_windowsettingspref', $resource->windowsettingspref);
    }

    return insert_record("resource", $resource);
}


function update_instance($resource) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    global $RESOURCE_WINDOW_OPTIONS;

    $resource->id = $resource->instance;
    $resource->timemodified = time();

    if (isset($resource->windowpopup)) {
        $optionlist = array();
        foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
            if (isset($resource->$option)) {
                $optionlist[] = $option."=".$resource->$option;
            }
        }
        $resource->popup = implode(',', $optionlist);

    } else if (isset($resource->windowpage)) {
        $resource->popup = "";
    }

    if (isset($resource->parametersettingspref)) {
        set_user_preference('resource_parametersettingspref', $resource->parametersettingspref);
    }
    if (isset($resource->windowsettingspref)) {
        set_user_preference('resource_windowsettingspref', $resource->windowsettingspref);
    }

    return update_record("resource", $resource);
}



function display() {
    global $CFG, $THEME;

    $course   = $this->course;    // Shortcut
    $resource = $this->resource;  // Shortcut

    $strresource = get_string("modulename", "resource");
    $strresources = get_string("modulenameplural", "resource");
    $strlastmodified = get_string("lastmodified");

    if ($course->category) {
        require_login($course->id);
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id={$course->id}\">{$course->shortname}</a> ->              
                       <a target=\"{$CFG->framename}\" href=\"index.php?id={$course->id}\">$strresources</a> ->";
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id={$course->id}\">$strresources</a> ->";
    }

    $pagetitle = strip_tags($course->shortname.': '.$resource->name);
    $formatoptions->noclean = true;
    $inpopup = !empty($_GET["inpopup"]);

    if ($resource->popup) {
        if ($inpopup) {                    /// Popup only
            add_to_log($course->id, "resource", "view", "view.php?id={$this->cm->id}", $resource->id, $this->cm->id);
            print_header();
            print_simple_box(format_text($resource->alltext, FORMAT_HTML, $formatoptions, $course->id), 
                                         "center", "", "$THEME->cellcontent", "20");
        } else {                           /// Make a page and a pop-up window

            print_header($pagetitle, $course->fullname, "$navigation {$resource->name}", 
                         "", "", true, update_module_button($this->cm->id, $course->id, $strresource), 
                         navmenu($course, $this->cm));

            echo "\n<script language=\"Javascript\">";
            echo "\n<!--\n";
            echo "openpopup('/mod/resource/view.php?inpopup=true&amp;id={$this->cm->id}','resource{$resource->id}','{$resource->popup}');\n";
            echo "\n-->\n";
            echo '</script>';
    
            if (trim(strip_tags($resource->summary))) {
                print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center");
            }
    
            $link = "<a href=\"$CFG->wwwroot/mod/resource/view.php?inpopup=true&amp;id={$this->cm->id}\" target=\"resource{$resource->id}\" onClick=\"return openpopup('/mod/resource/view.php?inpopup=true&amp;id={$this->cm->id}', 'resource{$resource->id}','{$resource->popup}');\">{$resource->name}</a>";
    
            echo "<p>&nbsp</p>";
            echo '<p align="center">';
            print_string('popupresource', 'resource');
            echo '<br />';
            print_string('popupresourcelink', 'resource', $link);
            echo "</p>";
    
            print_footer($course);
        }
    } else {    /// not a popup at all

        add_to_log($course->id, "resource", "view", "view.php?id={$this->cm->id}", $resource->id, $this->cm->id);
        print_header($pagetitle, $course->fullname, "$navigation {$resource->name}",
                     "", "", true, update_module_button($this->cm->id, $course->id, $strresource), 
                     navmenu($course, $this->cm));
    
        print_simple_box(format_text($resource->alltext, FORMAT_HTML, $formatoptions, $course->id), "center", "", "$THEME->cellcontent", "20");
    
        echo "<center><p><font size=\"1\">$strlastmodified: ".userdate($resource->timemodified)."</p></center>";
    
        print_footer($course);
    }
}



function setup($form) {
    global $CFG, $usehtmleditor, $RESOURCE_WINDOW_OPTIONS;
    
    parent::setup($form);


    $strfilename = get_string("filename", "resource");
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
                if ($optionname == "height" or $optionname == "width") {
                    $window->$optionname = $optionvalue;
                } else if ($optionvalue) {
                    $window->$optionname = "checked";
                }
            }
        }
    } else {
        foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
            $defaultvalue = "resource_popup$optionname";
            $window->$optionname = $CFG->$defaultvalue;
        }

        $windowtype = ($CFG->resource_popup) ? 'popup' : 'page';
        if (!isset($form->options)) {
            $form->options = '';
        }
    }

    include("$CFG->dirroot/mod/resource/type/html/html.html");

    parent::setup_end();
}


}

?>
