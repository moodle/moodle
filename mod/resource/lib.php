<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   mod-resource
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Require {@link portfoliolib.php} */
require_once($CFG->libdir.'/portfoliolib.php');

/** RESOURCE_LOCALPATH = LOCALPATH */
define('RESOURCE_LOCALPATH', 'LOCALPATH');

/**
 * @global array $RESOURCE_WINDOW_OPTIONS
 * @name $RESOURCE_WINDOW_OPTIONS
 */
global $RESOURCE_WINDOW_OPTIONS; // must be global because it might be included from a function!
$RESOURCE_WINDOW_OPTIONS = array('resizable', 'scrollbars', 'directories', 'location',
                                 'menubar', 'toolbar', 'status', 'width', 'height');

/**
 * resource_base is the base class for resource types
 *
 * This class provides all the functionality for a resource
 * @package   mod-resource
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resource_base {

    var $cm;
    var $course;
    var $resource;
    var $navlinks;

    /**
     * Constructor for the base resource class
     *
     * Constructor for the base resource class.
     * If cmid is set create the cm, course, resource objects.
     * and do some checks to make sure people can be here, and so on.
     *
     * @global stdClass
     * @global object
     * @global object
     * @uses CONTEXT_MODULE
     * @param int $cmid the current course module id - not set for new resources
     */
    function resource_base($cmid=0) {
        global $CFG, $COURSE, $DB;

        $this->navlinks = array();

        if ($cmid) {
            if (! $this->cm = get_coursemodule_from_id('resource', $cmid)) {
                print_error('invalidcoursemodule');
            }

            if (! $this->course = $DB->get_record("course", array("id"=>$this->cm->course))) {
                print_error('coursemisconf');
            }

            if (! $this->resource = $DB->get_record("resource", array("id"=>$this->cm->instance))) {
                print_error('invalidid', 'resource');
            }

            $this->strresource  = get_string("modulename", "resource");
            $this->strresources = get_string("modulenameplural", "resource");

            if (!$this->cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $this->cm->id))) {
                $pagetitle = strip_tags($this->course->shortname.': '.$this->strresource);
                $navigation = build_navigation($this->navlinks, $this->cm);

                print_header($pagetitle, $this->course->fullname, $navigation, "", "", true, '', navmenu($this->course, $this->cm));
                notice(get_string("activityiscurrentlyhidden"), "$CFG->wwwroot/course/view.php?id={$this->course->id}");
            }

        } else {
            $this->course = $COURSE;
        }
    }


    /**
    * Display function does nothing in the base class
    */
    function display() {

    }


    /**
     * Display the resource with the course blocks.
     *
     * @global stdClass
     * @uses PAGE_COURSE_VIEW
     * @uses PARAM_BOOL
     * @uses BLOCK_POS_LEFT
     * @uses BLOCK_POS_RIGHT
     */
    function display_course_blocks_start() {
        global $CFG, $USER, $THEME;

        require_once($CFG->dirroot.'/course/lib.php'); //required by some blocks

        $PAGE = page_create_object(PAGE_COURSE_VIEW, $this->course->id);
        $PAGE->set_url('mod/resource/view.php', array('id' => $this->cm->id));
        $this->PAGE = $PAGE;
        $pageblocks = blocks_setup($PAGE);

        $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

    /// Print the page header

        $edit = optional_param('edit', -1, PARAM_BOOL);

        if (($edit != -1) and $PAGE->user_allowed_editing()) {
            $USER->editing = $edit;
        }

        $morenavlinks = array($this->strresources   => 'index.php?id='.$this->course->id,
                                 $this->resource->name => '');

        $PAGE->print_header($this->course->shortname.': %fullname%', $morenavlinks, "", "",
                            update_module_button($this->cm->id, $this->course->id, $this->strresource));

        echo '<table id="layout-table"><tr>';

            $lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
        foreach ($lt as $column) {
            $lt1[] = $column;
            if ($column == 'middle') break;
        }
        foreach ($lt1 as $column) {
            switch ($column) {
                case 'left':
                    if((blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
                        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
                        print_container_start();
                        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
                        print_container_end();
                        echo '</td>';
                    }
                break;

                case 'middle':
                    echo '<td id="middle-column">';
                    print_container_start(false, 'middle-column-wrap');
                    echo '<div id="resource">';
                break;

                case 'right':
                    if((blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing())) {
                        echo '<td style="width: '.$blocks_preferred_width.'px;" id="right-column">';
                        print_container_start();
                        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
                        print_container_end();
                        echo '</td>';
                    }
                break;
            }
        }
    }


    /**
     * Finish displaying the resource with the course blocks
     *
     * @global stdClass
     * @global object
     * @uses BLOCK_POS_LEFT
     * @uses BLOCK_POS_RIGHT
     */
    function display_course_blocks_end() {
        global $CFG, $THEME;

        $PAGE = $this->PAGE;
        $pageblocks = blocks_setup($PAGE);
        $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);

        $lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
        foreach ($lt as $column) {
            if ($column != 'middle') {
                array_shift($lt);
            } else if ($column == 'middle') {
                break;
            }
        }
        foreach ($lt as $column) {
            switch ($column) {
                case 'left':
                    if((blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
                        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
                        print_container_start();
                        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
                        print_container_end();
                        echo '</td>';
                    }
                break;

                case 'middle':
                    echo '</div>';
                    print_container_end();
                    echo '</td>';
                break;

                case 'right':
                    if((blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing())) {
                        echo '<td style="width: '.$blocks_preferred_width.'px;" id="right-column">';
                        print_container_start();
                        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
                        print_container_end();
                        echo '</td>';
                    }
                break;
            }
        }

        echo '</tr></table>';

        print_footer($this->course);

    }

    /**
     * Given an object containing all the necessary data,
     * (defined by the form in mod_form.php) this function
     * will create a new instance and return the id number
     * of the new instance.
     *
     * @global object
     * @param object $resource
     * @return int|bool
     */
    function add_instance($resource) {
        global $DB;

        $resource->timemodified = time();

        return $DB->insert_record("resource", $resource);
    }

    /**
     * Given an object containing all the necessary data,
     * (defined by the form in mod_form.php) this function
     * will update an existing instance with new data.
     *
     * @global object
     * @param object $resource
     * @return bool
     */
    function update_instance($resource) {
        global $DB;

        $resource->id = $resource->instance;
        $resource->timemodified = time();

        return $DB->update_record("resource", $resource);
    }

    /**
     * Given an object containing the resource data
     * this function will permanently delete the instance
     * and any data that depends on it.
     *
     * @global object
     * @param object $resource
     * @return bool
     */
    function delete_instance($resource) {
        global $DB;

        $result = true;

        if (! $DB->delete_records("resource", array("id"=>$resource->id))) {
            $result = false;
        }

        return $result;
    }

    /**
     *
     */
    function setup_elements(&$mform) {
        //override to add your own options
    }

    /**
    *
    */
    function setup_preprocessing(&$default_values){
        //override to add your own options
    }

    /**
     * @todo penny implement later - see MDL-15758
     */
    function portfolio_prepare_package_uploaded($exporter) {
        // @todo penny implement later - see MDL-15758

    }

    /**
     * @uses FORMAT_MOODLE
     * @uses FORMAT_HTML
     * @param object $exporter
     * @param bool $text
     * @return int|bool
     */
    function portfolio_prepare_package_online($exporter, $text=false) {
        $filename = clean_filename($this->cm->name . '.' . 'html');
        $formatoptions = (object)array('noclean' => true);
        $format = (($text) ? FORMAT_MOODLE : FORMAT_HTML);
        $content = format_text($this->resource->alltext, $format, $formatoptions, $this->course->id);
        return $exporter->write_new_file($content, $filename, false);
    }

    /**
     * @param bool $text
     * @uses FORMAT_MOODLE
     * @uses FORMAT_HTML
     * @return string
     */
    function portfolio_get_sha1_online($text=false) {
        $formatoptions = (object)array('noclean' => true);
        $format = (($text) ? FORMAT_MOODLE : FORMAT_HTML);
        $content = format_text($this->resource->alltext, $format, $formatoptions, $this->course->id);
        return sha1($content);
    }

    /**
     * @todo penny implement later.
     */
    function portfolio_get_sha1_uploaded() {
        // @todo penny implement later.
    }

} /// end of class definition


/**
 * @global stdClass
 * @uses PARAM_SAFEDIR
 * @param object $resource
 * @return int|bool
 */
function resource_add_instance($resource) {
    global $CFG;

    $resource->type = clean_param($resource->type, PARAM_SAFEDIR);   // Just to be safe

    require_once("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $res = new $resourceclass();

    return $res->add_instance($resource);
}

/**
 * @global stdClass
 * @uses PARAM_SAFEDIR
 * @param object $resource
 * @return bool
 */
function resource_update_instance($resource) {
    global $CFG;

    $resource->type = clean_param($resource->type, PARAM_SAFEDIR);   // Just to be safe

    require_once("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $res = new $resourceclass();

    return $res->update_instance($resource);
}

/**
 * @global stdClass
 * @global object
 * @uses PARAM_SAFEDIR
 * @param int $id
 * @return bool
 */
function resource_delete_instance($id) {
    global $CFG, $DB;

    if (! $resource = $DB->get_record("resource", array("id"=>$id))) {
        return false;
    }

    $resource->type = clean_param($resource->type, PARAM_SAFEDIR);   // Just to be safe

    require_once("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $res = new $resourceclass();

    return $res->delete_instance($resource);
}

/**
 *
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $resource
 * @return object|null
 */
function resource_user_outline($course, $user, $mod, $resource) {
    global $DB;

    if ($logs = $DB->get_records("log", array('userid'=>$user->id, 'module'=>'resource',
                                              'action'=>'view', 'info'=>$resource->id), "time ASC")) {

        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $result = new object();
        $result->info = get_string("numviews", "", $numviews);
        $result->time = $lastlog->time;

        return $result;
    }
    return NULL;
}

/**
 * @global stdClass
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $resource
 */
function resource_user_complete($course, $user, $mod, $resource) {
    global $CFG, $DB;

    if ($logs = $DB->get_records("log", array('userid'=>$user->id, 'module'=>'resource',
                                              'action'=>'view', 'info'=>$resource->id), "time ASC")) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string("mostrecently");
        $strnumviews = get_string("numviews", "", $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string("neverseen", "resource");
    }
}

/**
 * Returns the users with data in one resource
 * (NONE, byt must exists on EVERY mod !!)
 *
 * @param int $resourceid
 * @return bool false
 */
function resource_get_participants($resourceid) {
    return false;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @global stdClass
 * @global object
 * @param object $coursemodule
 * @return object info
 */
function resource_get_coursemodule_info($coursemodule) {
   global $CFG, $DB;

   $info = NULL;

   if ($resource = $DB->get_record("resource", array("id"=>$coursemodule->instance), 'id, popup, reference, type, name')) {
       $info = new object();
       $info->name = $resource->name;
       if (!empty($resource->popup)) {
           $info->extra =  urlencode("onclick=\"this.target='resource$resource->id'; return ".
                            "openpopup('/mod/resource/view.php?inpopup=true&amp;id=".
                            $coursemodule->id.
                            "','resource$resource->id','$resource->popup');\"");
       }

       require_once($CFG->libdir.'/filelib.php');

       $customicon = $CFG->dirroot.'/mod/resource/type/'.$resource->type.'/icon.gif';
       if ($resource->type == 'file') {
           $icon = mimeinfo("icon", $resource->reference);
           if ($icon != 'unknown.gif') {
               $info->icon ="f/$icon";
           } else {
               $info->icon ="f/web.gif";
           }
       } else if ($resource->type == 'directory') {
           $info->icon ="f/folder.gif";
       } else if (file_exists($customicon)) {
           $info->icon ='mod/resource/type/'.$resource->type.'/icon.gif';
       }
   }

   return $info;
}

/**
 * @param string $text
 * @param string $url
 * @param string $tagtoparse
 * @param string $keytoparse
 * @param string $prefix
 * @return string
 */
function resource_redirect_tags($text, $url, $tagtoparse, $keytoparse,$prefix = "" ) {
    $valid = 1;
    if ( strpos($url,"?") == FALSE ) {
        $valid = 1;
    }
    if ( $valid ) {
        $lastpoint = strrpos($url,".");
        $lastslash = strrpos($url,"/");
        if ( $lastpoint > $lastslash ) {
            $root = substr($url,0,$lastslash+1);
        } else {
            $root = $url;
        }
        if ( $root == "http://" or
             $root == "https://") {
            $root = $url;
        }
        if ( substr($root,strlen($root)-1) == '/' ) {
            $root = substr($root,0,-1);
        }

        $mainroot = $root;
        $lastslash = strrpos($mainroot,"/");
        while ( $lastslash > 9) {
            $mainroot = substr($mainroot,0,$lastslash);

            $lastslash = strrpos($mainroot,"/");
        }

        $regex = "/<$tagtoparse (.+?)>/is";
        $count = preg_match_all($regex, $text, $hrefs);
        for ( $i = 0; $i < $count; $i++) {
            $tag = $hrefs[1][$i];

            $poshref = strpos(strtolower($tag),strtolower($keytoparse));
            $start = $poshref + strlen($keytoparse);
            $left = substr($tag,0,$start);
            if ( $tag[$start] == '"' ) {
                $left .= '"';
                $start++;
            }
            $posspace   = strpos($tag," ", $start+1);
            $right = "";
            if ( $posspace != FALSE) {
                $right = substr($tag, $posspace);
            }
            $end = strlen($tag)-1;
            if ( $tag[$end] == '"' ) {
                $right = '"' . $right;
            }
            $finalurl = substr($tag,$start,$end-$start+$diff);
            // Here, we could have these possible values for $finalurl:
            //     file.ext                             Add current root dir
            //     http://(domain)                      don't care
            //     http://(domain)/                     don't care
            //     http://(domain)/folder               don't care
            //     http://(domain)/folder/              don't care
            //     http://(domain)/folder/file.ext      don't care
            //     folder/                              Add current root dir
            //     folder/file.ext                      Add current root dir
            //     /folder/                             Add main root dir
            //     /folder/file.ext                     Add main root dir

            // Special case: If finalurl contains a ?, it won't be parsed
            $valid = 1;

            if ( strpos($finalurl,"?") == FALSE ) {
                $valid = 1;
            }
            if ( $valid ) {
                if ( $finalurl[0] == "/" ) {
                    $finalurl = $mainroot . $finalurl;
                } elseif ( strtolower(substr($finalurl,0,7)) != "http://" and
                           strtolower(substr($finalurl,0,8)) != "https://") {
                     if ( $finalurl[0] == "/") {
                        $finalurl = $mainroot . $finalurl;
                     } else {
                        $finalurl = "$root/$finalurl";
                     }
                }

                $text = str_replace($tag,"$left$prefix$finalurl$right",$text);
            }
        }
    }
    return $text;
}

/**
 * @param string $path
 * @return bool
 */
function resource_is_url($path) {
    if (strpos($path, '://')) {     // eg http:// https:// ftp://  etc
        return true;
    }
    if (strpos($path, '/') === 0) { // Starts with slash
        return true;
    }
    return false;
}

/**
 * @global stdClass
 * @uses MOD_CLASS_RESOURCE
 * @return array
 */
function resource_get_types() {
    global $CFG;

    $types = array();

    $standardresources = array('text','html','file','directory');
    foreach ($standardresources as $resourcetype) {
        $type = new object();
        $type->modclass = MOD_CLASS_RESOURCE;
        $type->name = $resourcetype;
        $type->type = "resource&amp;type=$resourcetype";
        $type->typestr =  resource_get_name($resourcetype);
        $types[] = $type;
    }

    /// Drop-in extra resource types
    $resourcetypes = get_plugin_list('resource');
    foreach ($resourcetypes as $resourcetype => $dir) {
        if (!empty($CFG->{'resource_hide_'.$resourcetype})) {  // Not wanted
            continue;
        }
        if (!in_array($resourcetype, $standardresources)) {
            $type = new object();
            $type->modclass = MOD_CLASS_RESOURCE;
            $type->name = $resourcetype;
            $type->type = "resource&amp;type=$resourcetype";
            $type->typestr = resource_get_name($resourcetype);
            $types[] = $type;
        }
    }

    return $types;
}

/**
 * @return array
 */
function resource_get_view_actions() {
    return array('view','view all');
}

/**
 * @return array
 */
function resource_get_post_actions() {
    return array();
}

/**
 * @global stdClass
 * @global object
 * @param object $course
 * @param string $wdir
 * @param string $oldname
 * @param string $name
 */
function resource_renamefiles($course, $wdir, $oldname, $name) {
    global $CFG, $DB;

    $status = '<p align=\"center\"><strong>'.get_string('affectedresources', 'resource').':</strong><ul>';
    $updates = false;

    $old = trim($wdir.'/'.$oldname, '/');
    $new = trim($wdir.'/'.$name, '/');

    $sql = "SELECT r.id, r.reference, r.name, cm.id AS cmid
              FROM {resource} r, {course_modules} cm, {modules} m
             WHERE r.course    = :courseid
                   AND m.name      = 'resource'
                   AND cm.module   = m.id
                   AND cm.instance = r.id
                   AND (r.type = 'file' OR r.type = 'directory')
                   AND (r.reference LIKE :old1 OR r.reference = :old2)";
    $params = array('courseid'=>$course->id, 'old1'=>"{$old}/%", 'old2'=>$old);
    if ($resources = $DB->get_records_sql($sql, $params)) {
        foreach ($resources as $resource) {
            $r = new object();
            $r->id = $resource->id;
            $r->reference = '';
            if ($resource->reference == $old) {
                $r->reference = $new;
            } else {
                $r->reference = preg_replace('|^'.preg_quote($old, '|').'/|', $new.'/', $resource->reference);
            }
            if ($r->reference !== '') {
                $updates = true;
                $status .= "<li><a href=\"$CFG->wwwroot/mod/resource/view.php?id=$resource->cmid\" target=\"_blank\">$resource->name</a>: $resource->reference ==> $r->reference</li>";
                if (!empty($CFG->resource_autofilerename)) {
                    $DB->update_record('resource', $r);
                }
            }
        }
    }
    $status .= '</ul></p>';

    if ($updates) {
        echo $status;
        if (empty($CFG->resource_autofilerename)) {
            notify(get_string('warningdisabledrename', 'resource'));
        }
    }
}

/**
 * @global stdClass
 * @global object
 * @param object $course
 * @param array $files
 * @return bool
 */
function resource_delete_warning($course, $files) {
    global $CFG, $DB;

    $found = array();

    foreach($files as $key=>$file) {
        $files[$key] = trim($file, '/');
    }
    $sql = "SELECT r.id, r.reference, r.name, cm.id AS cmid
              FROM {resource} r,
                   {course_modules} cm,
                   {modules} m
             WHERE r.course    = ?
                   AND m.name      = 'resource'
                   AND cm.module   = m.id
                   AND cm.instance = r.id
                   AND (r.type = 'file' OR r.type = 'directory')";
    if ($resources = $DB->get_records_sql($sql, array($course->id))) {
        foreach ($resources as $resource) {
            if ($resource->reference == '') {
                continue; // top shared directory does not prevent anything
            }
            if (in_array($resource->reference, $files)) {
                $found[$resource->id] = $resource;
            } else {
                foreach($files as $file) {
                    if (preg_match('|^'.preg_quote($file, '|').'/|', $resource->reference)) {
                        $found[$resource->id] = $resource;
                    }
                }
            }
        }
    }

    if (!empty($found)) {

        print_simple_box_start("center");
        echo '<p><strong>'.get_string('affectedresources', 'resource').':</strong><ul>';
        foreach($found as $resource) {
            echo "<li><a href=\"$CFG->wwwroot/mod/resource/view.php?id=$resource->cmid\" target=\"_blank\">$resource->name</a>: $resource->reference</li>";
        }
        echo '</ul></p>';
        print_simple_box_end();

        return true;
    } else {
        return false;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * 
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function resource_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function resource_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @package   mod-resource
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resource_portfolio_caller extends portfolio_module_caller_base {

    private $resource;
    private $resourcefile;

    /**
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'id' => true,
        );
    }

    /**
     * @global stdClass
     * @global object
     */
    public function load_data() {
        global $CFG, $DB;
        if (!$this->cm = get_coursemodule_from_instance('resource', $this->id)) {
            throw new portfolio_caller_exception('invalidid');
        }
        $this->cm->type = $DB->get_field('resource', 'type', array('id' => $this->cm->instance));
        $resourceclass = 'resource_'. $this->cm->type;
        $this->resourcefile = $CFG->dirroot.'/mod/resource/type/'.$this->cm->type.'/resource.class.php';
        require_once($this->resourcefile);
        $this->resource= new $resourceclass($this->cm->id);
        if (!is_callable(array($this->resource, 'portfolio_prepare_package')) || !is_callable(array($this->resource, 'portfolio_get_sha1'))) {
            throw new portfolio_exception('portfolionotimplemented', 'resource', null, $this->cm->type);
        }
        $this->supportedformats = array(self::type_to_format($this->cm->type));
    }

    /**
     * @uses PORTFOLIO_FORMAT_FILE
     * @uses PORTFOLIO_FORMAT_PLAINHTML
     * @uses PORTFOLIO_FORMAT_TEXT
     * @param string $type
     * @return string
     */
    public static function type_to_format($type) {
        // this is kind of yuk... but there's just not good enough OO here
        $format = PORTFOLIO_FORMAT_FILE;
        switch ($type) {
            case 'html':
                $format = PORTFOLIO_FORMAT_PLAINHTML;
            case 'text':
                $format = PORTFOLIO_FORMAT_TEXT;
            case 'file':
                // $format = portfolio_format_from_file($file); // change after we switch upload type resources over to new files api.
        }
        return $format;
    }

    /**
     * @global stdClass
     * @return void
     */
    public function __wakeup() {
        global $CFG;
        if (empty($CFG)) {
            return; // too early yet
        }
        require_once($this->resourcefile);
        $this->resource = unserialize(serialize($this->resource));
    }

    /**
     * @todo penny check filesize if the type is uploadey (not implemented yet)
     * like this: return portfolio_expected_time_file($this->file); or whatever
     *
     * @return string
     */
    public function expected_time() {
        return PORTFOLIO_TIME_LOW;
    }

    /**
     *
     */
    public function prepare_package() {
        return $this->resource->portfolio_prepare_package($this->exporter);
    }

    /**
     * @uses CONTEXT_MODULE
     * @return bool
     */
    public function check_permissions() {
        return has_capability('mod/resource:exportresource', get_context_instance(CONTEXT_MODULE, $this->cm->id));
    }

    /**
     * @uses CONTEXT_MODULE
     * @param object $resource
     * @param mixed $format
     * @param bool $return
     * @return mixed
     */
    public static function add_button($resource, $format=null, $return=false) {
        if (!has_capability('mod/resource:exportresource', get_context_instance(CONTEXT_MODULE, $resource->cm->id))) {
            return;
        }
        if (!is_callable(array($resource, 'portfolio_prepare_package')) || !is_callable(array($resource, 'portfolio_get_sha1'))) {
            debugging(get_string('portfolionotimplemented', 'resource'));
            return false;
        }
        $callersupports = array(self::type_to_format($resource->resource->type));
        if ($resource->resource->type == 'file') {
            // $callersupports = array(portfolio_format_from_file($file);
        }
        $button = new portfolio_add_button();
        $button->set_callback_options('resource_portfolio_caller', array('id' => $resource->cm->instance),  '/mod/resource/lib.php');
        $button->set_formats($callersupports);
        if ($return) {
            return $button->to_html($format);
        }
        $button->render($format);
    }

    /**
     * @return string
     */
    public function get_sha1() {
        return $this->resource->portfolio_get_sha1();
    }

    /**
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'resource');
    }
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function resource_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_MOD_SUBPLUGINS:          return array('resource'=>'mod/resource/type'); // to be removed in 2.0

        default: return null;
    }
}

/**
 * Returns the full name of the given resource type.  The name can
 * either be set at the resource type level or at the resource module
 * level.
 *
 * @param string $type shortname (or directory name) of the resource type
 * @return string
 */
function resource_get_name($type) {
    $name = get_string("resourcetype$type", "resource_$type");
    if (substr($name, 0, 2) === '[[') {
        $name = get_string("resourcetype$type", 'resource');
    }
    return $name;
}
