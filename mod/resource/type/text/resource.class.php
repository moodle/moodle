<?php // $Id$

class resource_text extends resource_base {


function resource_text($cmid=0) {
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

    if (isset($resource->blockdisplay)) {
        $resource->options = 'showblocks';
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

    if (isset($resource->blockdisplay)) {
        $resource->options = 'showblocks';
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
    global $CFG;


    /// Are we displaying the course blocks?
    if ($this->resource->options == 'showblocks') {

        parent::display_course_blocks_start();

        $formatoptions->noclean = true;
        
        if (trim(strip_tags($this->resource->alltext))) {
            echo format_text($this->resource->alltext, FORMAT_MOODLE, $formatoptions, $this->course->id);
        }

        parent::display_course_blocks_end();

    } else {

        /// Set up generic stuff first, including checking for access
        parent::display();

        /// Set up some shorthand variables
        $cm = $this->cm;     
        $course = $this->course;
        $resource = $this->resource; 

        $pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));
        $formatoptions->noclean = true;
        $inpopup_param = optional_param( 'inpopup', '' );
        $inpopup = !empty($inpopup_param);

        if ($resource->popup) {
            if ($inpopup) {                    /// Popup only
                add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", 
                        $resource->id, $cm->id);
                print_header();
                print_simple_box(format_text($resource->alltext, $resource->options, $formatoptions, $course->id), 
                        "center", "", "", "20");
                print_footer($course);
            } else {                           /// Make a page and a pop-up window

                print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name), 
                        "", "", true, update_module_button($cm->id, $course->id, $this->strresource), 
                        navmenu($course, $cm));

                echo "\n<script language=\"javascript\" type=\"text/javascript\">";
                echo "\n<!--\n";
                echo "openpopup('/mod/resource/view.php?inpopup=true&id={$cm->id}','resource{$resource->id}','{$resource->popup}');\n";
                echo "\n-->\n";
                echo '</script>';

                if (trim(strip_tags($resource->summary))) {
                    print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center");
                }

                $link = "<a href=\"$CFG->wwwroot/mod/resource/view.php?inpopup=true&amp;id={$cm->id}\" target=\"resource{$resource->id}\" onclick=\"return openpopup('/mod/resource/view.php?inpopup=true&amp;id={$cm->id}', 'resource{$resource->id}','{$resource->popup}');\">".format_string($resource->name,true)."</a>";

                echo "<p>&nbsp</p>";
                echo '<p align="center">';
                print_string('popupresource', 'resource');
                echo '<br />';
                print_string('popupresourcelink', 'resource', $link);
                echo "</p>";

                print_footer($course);
            }
        } else {    /// not a popup at all

            add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);
            print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name),
                    "", "", true, update_module_button($cm->id, $course->id, $this->strresource), 
                    navmenu($course, $cm));

            print_simple_box(format_text($resource->alltext, $resource->options, $formatoptions, $course->id), 
                    "center", "", "", "20");

            $strlastmodified = get_string("lastmodified");
            echo "<center><p><font size=\"1\">$strlastmodified: ".userdate($resource->timemodified)."</font></p></center>";

            print_footer($course);
        }

    }

}



function setup($form) {
    global $CFG, $editorfields, $RESOURCE_WINDOW_OPTIONS;

    $editorfields = 'summary';
    
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
    
    $blockoption = "\"blockdisplay\"";
    $popupoptions = implode(",", $jsoption);
    $jsoption[] = $blockoption;
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
                    $window->$optionname = 'checked="checked"';
                }
            }
        }
    } else {
        foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
            $defaultvalue = "resource_popup$optionname";
            
            if ($optionname == "height" or $optionname == "width") {
                $window->$optionname = $CFG->$defaultvalue;
            } else if ($CFG->$defaultvalue) {
                $window->$optionname = 'checked="checked"';
            }
        }

        $windowtype = ($CFG->resource_popup) ? 'popup' : 'page';
        if (!isset($form->options)) {
            $form->options = '';
        }
    }

    $format_array = format_text_menu();
    unset($format_array[FORMAT_HTML]);
    include("$CFG->dirroot/mod/resource/type/text/text.html");

    parent::setup_end();
}


}

?>
