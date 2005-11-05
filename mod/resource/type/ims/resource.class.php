<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
* Extend the base resource class for ims resources
*/
class resource_ims extends resource_base {

    var $parameters;  //Attribute of this class where we'll store all the IMS deploy preferences

    function resource_ims($cmid=0) {
    /// super constructor
        parent::resource_base($cmid);

    /// prevent notice
        if (empty($this->resource->alltext)) {
            $this->resource->alltext='';
        }
    /// set own attributes
        $this->parameters = $this->alltext2parameters($this->resource->alltext);
    }

    /***
    * This function converts parameters stored in the alltext field to the proper 
    * this->parameters object storing the special configuration of this resource type
    */
    function alltext2parameters($alltext) {
        /// set parameter defaults
        $alltextfield = new stdClass();
        $alltextfield->tableofcontents=0;
        $alltextfield->navigationbuttons=0;

    /// load up any stored parameters
        if (!empty($alltext)) {
            $parray = explode(',', $alltext);
            foreach ($parray as $key => $fieldstring) {
                $field = explode('=', $fieldstring);
                $alltextfield->$field[0] = $field[1];
            }
        }

        return $alltextfield;
    }

    /***
    * This function converts the this->parameters attribute (object) to the format
    * needed to save them in the alltext field to store all the special configuration
    * of this resource type
    */
    function parameters2alltext($parameters) {
        $optionlist = array();

        $optionlist[] = 'tableofcontents='.$parameters->tableofcontents;
        $optionlist[] = 'navigationbuttons='.$parameters->navigationbuttons;

        return implode(',', $optionlist);
    }

    /***
    * This function will convert all the parameters configured in the resource form
    * to a this->parameter attribute (object)
    */
    function form2parameters($resource) {
        $parameters = new stdClass;
        $parameters->tableofcontents = $resource->param_tableofcontents;
        $parameters->navigationbuttons = $resource->param_navigationbuttons;

        return $parameters;
    }

    /*** This function checks for errors in the status or deployment of the IMS
    * Content Package returning an error code:
    * 1 = Not a .zip file.
    * 2 = Zip file doesn't exist
    * 3 = Package not deployed.
    * 4 = Package has changed since deployed.
    */
    function check4errors($file, $course, $resource) {

        global $CFG;

        $mimetype = mimeinfo("type", $file);
        if ($mimetype != "application/zip") {
            return 1;    //Error
        }

    /// Check if the uploaded file exists
        if (!file_exists($CFG->dataroot.'/'.$course->id.'/'.$file)) {
            return 2;    //Error
        }

    /// Calculate the path were the IMS package must be deployed
        $deploydir = $CFG->dataroot.'/'.$course->id.'/'.$CFG->moddata.'/resource/'.$resource->id;

    /// Confirm that the IMS package has been deployed. These files must exist if
    /// the package is deployed: moodle_index.ser and moodle_hash.ser
        if (!file_exists($deploydir.'/moodle_inx.ser') ||
            !file_exists($deploydir.'/moodle_hash.ser')) {
            return 3;    //Error
        }

    /// If teacheredit, make, hash check. It's the md5 of the name of the file 
    /// plus its size and modification date
        if (isteacheredit($course->id)) {
            if (!$this->checkpackagehash($file, $course, $resource)) {
                return 4;
            }
        }

    /// We've arrived here. Everything is ok
        return 0;
    }

    /*** This function will check that the ims package (zip file) uploaded 
    * isn't changed since it was deployed.
    */
    function checkpackagehash($file, $course, $resource) {
        global $CFG;

    /// Calculate paths
        $zipfile = $CFG->dataroot.'/'.$course->id.'/'.$file;
        $hashfile= $CFG->dataroot.'/'.$course->id.'/'.$CFG->moddata.'/resource/'.$resource->id.'/moodle_hash.ser';
    /// Get deloyed hash value
        $f = fopen ($hashfile,'r');
        $deployedhash = fread($f, filesize($hashfile));
        fclose ($f);
    /// Unserialize the deployed hash
        $deployedhash = unserialize($deployedhash);
    /// Calculate uploaded file hash value
        $uploadedhash = $this->calculatefilehash($zipfile);

    /// Compare them
        return ($deployedhash == $uploadedhash);
    }

    /*** This function will calculate the hash of any file passes as argument.
    * It's based in a md5 of the filename, filesize and 20 first bytes (it includes
    * the zip CRC at byte 15).
    */
    function calculatefilehash($filefullpath) {

    /// Name and size
        $filename = basename($filefullpath);
        $filesize = filesize($filefullpath);
    /// Read first 20cc
        $f = fopen ($filefullpath,'r');
        $data = fread($f, 20);
        fclose ($f);

        return md5($filename.'-'.$filesize.'-'.$data);
    }

    /**
    * Add new instance of file resource
    *
    * Create alltext field before calling base class function.
    *
    * @param    resource object
    */
    function add_instance($resource) {

    /// Load parameters to this->parameters
        $this->parameters = $this->form2parameters($resource);
    /// Save parameters into the alltext field
        $resource->alltext = $this->parameters2alltext($this->parameters);

        return parent::add_instance($resource);
    }


    /**
    * Update instance of file resource
    *
    * Create alltext field before calling base class function.
    *
    * @param    resource object
    */
    function update_instance($resource) {

    /// Load parameters to this->parameters
        $this->parameters = $this->form2parameters($resource);
    /// Save parameters into the alltext field
        $resource->alltext = $this->parameters2alltext($this->parameters);

        return parent::update_instance($resource);
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

        require_once($CFG->libdir.'/filelib.php');

    /// Set up generic stuff first, including checking for access
        parent::display();

    /// Set up some shorthand variables
        $cm = $this->cm;
        $course = $this->course;
        $resource = $this->resource;

    /// Fetch parameters
        $inpopup = optional_param('inpopup', 0, PARAM_BOOL);
        $page    = optional_param('page', 0, PARAM_INT);
        $frameset= optional_param('frameset', '', PARAM_ALPHA);

    /// Init some variables
        $errorcode = 0;
        $buttontext = 0;
        $querystring = '';
        $resourcetype = '';
        $mimetype = mimeinfo("type", $resource->reference);
        $pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));

    /// Cache this per request
        static $items;

    /// Check for errors
        $errorcode = $this->check4errors($resource->reference, $course, $resource);

    /// If there are any error, show it instead of the resource page
        if ($errorcode) {
            if (!isteacheredit($course->id)) {
            /// Resource not available page
                $errortext = get_string('resourcenotavailable','resource');
            } else {
            /// Depending of the error, show different messages and pages
                if ($errorcode ==1) {
                    $errortext = get_string('invalidfiletype','resource');
                } else if ($errorcode == 2) {
                    $errortext = get_string('filenotexists','resource');
                } else if ($errorcode == 3) {
                    $errortext = get_string('packagenotdeplyed','resource');
                } else if ($errorcode == 4) {
                    $errortext = get_string('packagechanged','resource');
                }
            }
        /// Display the error and exit
            if ($inpopup) {
                print_header($pagetitle, $course->fullname.' : '.$resource->name);
            } else {
                print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name), "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm));
            }
            print_simple_box_start('center', '60%');
            echo '<p align="center">'.$errortext.'</p>';
        /// If errors were 3 or 4 and isteacheredit(), show the deploy button
            if (isteacheredit($course->id) && ($errorcode = 3 || $errorcode ==4)) {
                $link = 'type/ims/deploy.php';
                $options['courseid'] = $course->id;
                $options['cmid'] = $cm->id;
                $options['file'] = $resource->reference;
                $options['sesskey'] = $USER->sesskey;
                $options['inpopup'] = $inpopup;
                if ($errorcode == 3) {
                    $label = get_string ('deploy', 'resource');
                } else if ($errorcode == 4) {
                     $label = get_string ('redeploy', 'resource');
                }
                $method='post';
            /// Let's go with the button
                echo '<center>';
                print_single_button($link, $options, $label, $method);
                echo '</center>';
            }
            print_simple_box_end();
        /// Close button if inpopup
            if ($inpopup) {
                close_window_button();
            }

            print_footer();
            exit;
        }

    /// Load serialized IMS CP index to memory only once.
        if (empty($items)) {
            $resourcedir = $CFG->dataroot.'/'.$course->id.'/'.$CFG->moddata.'/resource/'.$resource->id;
            if (!$items = ims_load_serialized_file($resourcedir.'/moodle_inx.ser')) {
                error (get_string('errorreadingfile', 'error', 'moodle_inx.ser'));
            }
        }

    /// Check whether this is supposed to be a popup, but was called directly

        if (empty($frameset) && $resource->popup && !$inpopup) {    /// Make a page and a pop-up window

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


    /// If we aren't in a frame, build it (the main one)

        if (empty($frameset)) {

        /// Select encoding
            if (!empty($CFG->unicode)) {
                $encoding = 'utf-8';
            } else {
                $encoding = get_string('thischarset');
            }

        /// Select direction
            if (get_string('thisdirection') == 'rtl') {
                $direction = ' dir="rtl"';
            } else {
                $direction = ' dir="ltr"';
            }

        /// The frameset output starts

            echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
            echo "<html$direction>\n";
            echo '<head>';
            echo '<meta http-equiv="content-type" content="text/html; charset='.$encoding.'" />';
            echo "<title>{$course->shortname}: ".strip_tags(format_string($resource->name,true))."</title></head>\n";
            echo "<frameset rows=\"$CFG->resource_framesize,*\">"; //Main frameset
            echo "<frame src=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;frameset=top\" />"; //Top frame
            echo "<frame src=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;frameset=ims\" />"; //Ims frame
            echo "</frameset>";
            echo "</html>";
        /// We can only get here once per resource, so add an entry to the log
            add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);
            exit;
        }

    /// If required we print the ims frameset

        if ($frameset == 'ims') {

        /// Calculate the file.php correct url
            if ($CFG->slasharguments) {
                $fileurl = "{$CFG->wwwroot}/file.php/{$course->id}/{$CFG->moddata}/resource/{$resource->id}";
            } else {
                $fileurl = "{$CFG->wwwroot}/file.php?file=/{$course->id}/{$CFG->moddata}/resource/{$resource->id}";
            }

        /// Calculate the view.php correct url
            $viewurl = "view.php?id={$cm->id}&amp;type={$resource->type}&amp;frameset=toc&amp;page=";


        /// Decide what to show (full toc, partial toc or package file)
            $fullurl = '';
            if (empty($page) && !empty($this->parameters->tableofcontents)) {
            /// Full toc contents
                $fullurl = $viewurl.$page;
            } else {
                if (empty($page)) {
                /// If no page and no toc, set page 1
                    $page = 1;
                }
                if (empty($items[$page]->href)) {
                /// The page hasn't href, then partial toc contents
                    $fullurl = $viewurl.$page;
                } else {
                /// The page has href, then its own file contents
                /// but considering if it seems to be an external url or a internal one
                    if (strpos($items[$page]->href, '//') !== false) {
                    /// External URL
                        $fullurl = $items[$page]->href;
                    } else {
                    /// Internal URL, use file.php
                        $fullurl = $fileurl.'/'.$items[$page]->href;
                    }
                }
            }

        /// Select encoding
            if (!empty($CFG->unicode)) {
                $encoding = 'utf-8';
            } else {
                $encoding = get_string('thischarset');
            }

        /// Select direction
            if (get_string('thisdirection') == 'rtl') {
                $direction = ' dir="rtl"';
            } else {
                $direction = ' dir="ltr"';
            }

        /// The frameset output starts

            echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
            echo "<html$direction>\n";
            echo '<head>';
            echo '<meta http-equiv="content-type" content="text/html; charset='.$encoding.'" />';
            echo "<title>{$course->shortname}: ".strip_tags(format_string($resource->name,true))."</title></head>\n";
            if (!empty($this->parameters->navigationbuttons)) {
                echo "<frameset rows=\"20,*\" border=\"0\">"; //Ims frameset with navigation buttons
            } else {
                echo "<frameset rows=\"*\">";    //Ims frameset without navigation buttons
            }
            if (!empty($this->parameters->navigationbuttons)) {
                echo "<frame src=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;page={$page}&amp;frameset=nav\" scrolling=\"no\" noresize=\"noresize\" name=\"ims-nav\" />"; //Nav frame
            }
            echo "<frame src=\"{$fullurl}\" name=\"ims-content\" />"; //Content frame
            echo "</frameset>";
            echo "</html>";
            exit;
        }

    /// If we are in the top frameset, just print it

        if ($frameset == 'top') {

        /// The header depends of the resource->popup
            if ($resource->popup) {
                print_header($pagetitle, $course->fullname.' : '.$resource->name);
            } else {
                print_header($pagetitle, $course->fullname, "$this->navigation ".format_string($resource->name), "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "parent"));
            }
            echo '</body></html>';
            exit;
        }

    /// If we are in the toc frameset, calculate and show the toc autobuilt page

        if ($frameset == 'toc') {
            print_header();
            $table = new stdClass;
            if (empty($page)) {
                $table->head[] = '<b>'.$resource->name.'</b>';
            } else {
                $table->head[] = '<b>'.$items[$page]->title.'</b>';
            }
            $table->data[] = array(ims_generate_toc ($items, $resource, $page));
            $table->width = '60%';
            print_table($table);
            print_footer();
            exit;
        }
        
    /// If we are in the nav frameset, just print it

        if ($frameset == 'nav') {
        /// Header
            print_header();
            echo '<div class="ims-nav-bar">';
        /// Prev button
            echo ims_get_prev_nav_button ($items, $this, $page);
        /// Up button
            echo ims_get_up_nav_button ($items, $this, $page);
        /// Next button
            echo ims_get_next_nav_button ($items, $this, $page);
        /// Main TOC button
            echo ims_get_toc_nav_button ($items, $this, $page);
        /// Footer
            echo '</div></div></div></body></html>';
            exit;
        }
    }


    /**
    * Setup a new file resource
    *
    * Display a form to create a new or edit an existing file resource
    *
    * @param    form                    object
    * @param    CFG                     global object
    * @param    usehtmleditor           global integer
    * @param    RESOURCE_WINDOW_OPTIONS global array
    */
    function setup($form) {
        global $CFG, $usehtmleditor, $RESOURCE_WINDOW_OPTIONS;

        parent::setup($form);

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

        //Converts the alltext to form fields
        $parameters=$this->alltext2parameters($form->alltext);
        $form->param_tableofcontents = $parameters->tableofcontents;
        $form->param_navigationbuttons = $parameters->navigationbuttons;

        //Show the setup form
        include("$CFG->dirroot/mod/resource/type/ims/ims.html");

        parent::setup_end();
    }

} //End class

///
/// General purpose functions
///
    /*** This function will serialize the variable passed and send it
     *   to filesystem
     */
    function ims_save_serialized_file($destination, $var) {
        $status = false;
        if ($ser = serialize($var)) {
            $status = ims_var2file($destination, $ser);
        }
        return $status;
     }

    /*** This function will unserialize the variable stored
     *   in filesystem
     */
    function ims_load_serialized_file($file) {
        $status = false;
        if ($ser = ims_file2var($file)) {
            $status = unserialize($ser);
        }
        return $status;
    }

    /*** This function will load all the contents of one file to one variable
     *   Not suitable for BIG files
     */
    function ims_file2var ($file) {
        $status = true;
        $var = '';
        $fp = fopen($file, 'r')
            or $status = false;
        if ($status) {
           while ($data = fread($fp, 4096)) {
               $var = $var.$data;
           }
           fclose($fp);
        }
        if (!$status) {
            $var = false;
        }
        return $var;
    }

    /*** This file will write the contents of one variable to a file
     *   Not suitable for BIG files
     */
    function ims_var2file ($file, $var) {
        $status = false;
        if ($out = fopen($file,"w")) {
            $status = fwrite($out, $var);
            fclose($out);
        }
        return $status;
    }

    /*** This function will generate the TOC file for the package
     *   from an specified parent to be used in the view of the IMS
     */
    function ims_generate_toc($items, $resource, $page=0) {
        global $CFG,$SESSION;

        $contents = '';

    /// Configure links behaviour
        $fullurl = $CFG->wwwroot.'/mod/resource/view.php?r='.$resource->id.'&amp;frameset=ims&amp;page=';

    /// Decide if we have to leave text in UTF-8, else convert to ISO-8859-1
    /// (interim solution until everything was migrated to UTF-8). Then we'll
    //  delete this hack.
        $convert = true;
        if ($SESSION->encoding == 'UTF-8') {
            $convert = false;
        }

    /// Iterate over items to build the menu
        $currlevel = 0;
        $currorder = 0;
        $endlevel  = 0;
        foreach ($items as $item) {
        /// Convert text to ISO-8859-1 if specified (will remove this once utf-8 migration was complete- 1.6)
        if ($convert) {
            $item->title = utf8_decode($item->title);
        }
        
        /// Skip pages until we arrive to $page
            if ($item->id < $page) {
                continue;
            }
        /// Arrive to page, we store its level
            if ($item->id == $page) {
                $endlevel = $item->level;
                continue;
            }
        /// We are after page and inside it (level > endlevel)
            if ($item->id > $page && $item->level > $endlevel) {
            /// Start Level 
                if ($item->level > $currlevel) {
                    $contents .= '<ol class="listlevel_'.$item->level.'">';
                }
            /// End Level
                if ($item->level < $currlevel) {
                    $contents .= '</ol>';
                }
            /// Add item
                $contents .= '<li>';
                if (!empty($item->href)) {
                    $contents .= '<a href="'.$fullurl.$item->id.'" target="_parent">'.$item->title.'</a>';
                } else {
                    $contents .= $item->title;
                }
                $contents .= '</li>';
                $currlevel = $item->level;
                continue;
            }
        /// We have reached endlevel, exit
            if ($item->id > $page && $item->level <= $endlevel) {
                break;
            }
        }
        $contents .= '</ol>';

        return $contents;
    }

    /*** This function will return the correct html code needed
     *   to show the previous button in the nav frame
     **/
    function ims_get_prev_nav_button ($items, $resource_obj, $page) {

        $cm = $resource_obj->cm;
        $resource = $resource_obj->resource;

        $contents = '';

        if ($page > 1 ) {  //0 and 1 pages haven't previous
            $page--;
            $contents .= "<span class=\"ims-nav-button\"><a href=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;page={$page}&amp;frameset=ims\" target=\"_parent\">&lt;&lt;</a></span>"; 
        } else {
            $contents .= '<span class="ims-nav-dimmed">&lt;&lt;</span>';
        }

        return $contents;
    }

    /*** This function will return the correct html code needed
     *   to show the next button in the nav frame
     **/
    function ims_get_next_nav_button ($items, $resource_obj, $page) {

        $cm = $resource_obj->cm;
        $resource = $resource_obj->resource;

        $contents = '';

        if (!empty($items[$page+1])) {  //If the next page exists
            $page++;
            $contents .= "<span class=\"ims-nav-button\"><a href=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;page={$page}&amp;frameset=ims\" target=\"_parent\">&gt;&gt;</a></span>";
        } else {
            $contents .= '<span class="ims-nav-dimmed">&gt;&gt;</span>';
        }


        return $contents;
    }

    /*** This function will return the correct html code needed
     *   to show the up button in the nav frame
     **/
    function ims_get_up_nav_button ($items, $resource_obj, $page) {

        $cm = $resource_obj->cm;
        $resource = $resource_obj->resource;

        $contents = '';

        if ($page > 1 && $items[$page]->parent > 0 ) {  //If the page has parent
            $page = $items[$page]->parent;
            $contents .= "<span class=\"ims-nav-button\"><a href=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;page={$page}&amp;frameset=ims\" target=\"_parent\">&and;</a></span>";
        } else {
            $contents .= '<span class="ims-nav-dimmed">&and;</span>';
        }

        return $contents;
    }

    /*** This function will return the correct html code needed
     *   to show the toc button in the nav frame
     **/
    function ims_get_toc_nav_button ($items, $resource_obj, $page) {

        $cm = $resource_obj->cm;
        $resource = $resource_obj->resource;

        $strtoc = get_string('toc', 'resource');

        $contents = '';

        if (!empty($resource_obj->parameters->tableofcontents)) {  //The toc is enabled
            $page = 0;
            $contents .= "<span class=\"ims-nav-button\"><a href=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;page={$page}&amp;frameset=ims\" target=\"_parent\">TOC</a></span>";
        }

        return $contents;
    }

?>
