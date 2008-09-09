<?php  // $Id$

define('RESOURCE_LOCALPATH', 'LOCALPATH');

global $RESOURCE_WINDOW_OPTIONS; // must be global because it might be included from a function!
$RESOURCE_WINDOW_OPTIONS = array('resizable', 'scrollbars', 'directories', 'location',
                                 'menubar', 'toolbar', 'status', 'width', 'height');

if (!isset($CFG->resource_hide_repository)) {
    set_config("resource_hide_repository", "1");
}

/**
* resource_base is the base class for resource types
*
* This class provides all the functionality for a resource
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
    * @param cmid   integer, the current course module id - not set for new resources
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
    */
    function display_course_blocks_start() {

        global $CFG, $USER, $THEME;

        require_once($CFG->libdir.'/blocklib.php');
        require_once($CFG->libdir.'/pagelib.php');
        require_once($CFG->dirroot.'/course/lib.php'); //required by some blocks

        $PAGE = page_create_object(PAGE_COURSE_VIEW, $this->course->id);
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


    function add_instance($resource) {
        global $DB;
    // Given an object containing all the necessary data,
    // (defined by the form in mod_form.php) this function
    // will create a new instance and return the id number
    // of the new instance.

        $resource->timemodified = time();

        return $DB->insert_record("resource", $resource);
    }


    function update_instance($resource) {
        global $DB;
    // Given an object containing all the necessary data,
    // (defined by the form in mod_form.php) this function
    // will update an existing instance with new data.

        $resource->id = $resource->instance;
        $resource->timemodified = time();

        return $DB->update_record("resource", $resource);
    }


    function delete_instance($resource) {
        global $DB;
    // Given an object containing the resource data
    // this function will permanently delete the instance
    // and any data that depends on it.

        $result = true;

        if (! $DB->delete_records("resource", array("id"=>$resource->id))) {
            $result = false;
        }

        return $result;
    }

    function setup_elements(&$mform) {
        //override to add your own options
    }

    function setup_preprocessing(&$default_values){
        //override to add your own options
    }

    function portfolio_prepare_package_uploaded($exporter) {
        // @todo penny implement later - see MDL-15758

    }

    function portfolio_prepare_package_online($exporter, $text=false) {
        $filename = clean_filename($this->cm->name . '.' . (($text) ? 'txt' : 'html'));
        $formatoptions = (object)array('noclean' => true);
        $format = (($text) ? FORMAT_MOODLE : FORMAT_HTML);
        $content = format_text($this->resource->alltext, $format, $formatoptions, $this->course->id);
        return $exporter->write_new_file($content, $filename);
    }

    function portfolio_get_sha1_online($text=false) {
        $formatoptions = (object)array('noclean' => true);
        $format = (($text) ? FORMAT_MOODLE : FORMAT_HTML);
        $content = format_text($this->resource->alltext, $format, $formatoptions, $this->course->id);
        return sha1($content);
    }

    function portfolio_get_sha1_uploaded() {
        // @todo penny implement later.
    }

} /// end of class definition



function resource_add_instance($resource) {
    global $CFG;

    $resource->type = clean_param($resource->type, PARAM_SAFEDIR);   // Just to be safe

    require_once("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $res = new $resourceclass();

    return $res->add_instance($resource);
}

function resource_update_instance($resource) {
    global $CFG;

    $resource->type = clean_param($resource->type, PARAM_SAFEDIR);   // Just to be safe

    require_once("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $res = new $resourceclass();

    return $res->update_instance($resource);
}

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

function resource_get_participants($resourceid) {
//Returns the users with data in one resource
//(NONE, byt must exists on EVERY mod !!)

    return false;
}

function resource_get_coursemodule_info($coursemodule) {
/// Given a course_module object, this function returns any
/// "extra" information that may be needed when printing
/// this activity in a course listing.
///
/// See get_array_of_activities() in course/lib.php
///
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

       if ($resource->type == 'file') {
           $icon = mimeinfo("icon", $resource->reference);
           if ($icon != 'unknown.gif') {
               $info->icon ="f/$icon";
           } else {
               $info->icon ="f/web.gif";
           }
       } else if ($resource->type == 'directory') {
           $info->icon ="f/folder.gif";
       }
   }

   return $info;
}

function resource_fetch_remote_file ($cm, $url, $headers = "" ) {
/// Snoopy is an HTTP client in PHP

    global $CFG;

    require_once("$CFG->libdir/snoopy/Snoopy.class.inc");

    $client = new Snoopy();
    $ua = 'Moodle/'. $CFG->release . ' (+http://moodle.org';
    if ( $CFG->resource_usecache ) {
        $ua = $ua . ')';
    } else {
        $ua = $ua . '; No cache)';
    }
    $client->agent = $ua;
    $client->read_timeout = 5;
    $client->use_gzip = true;
    if (is_array($headers) ) {
        $client->rawheaders = $headers;
    }

    @$client->fetch($url);
    if ( $client->status >= 200 && $client->status < 300 ) {
        $tags = array("A"      => "href=",
                      "IMG"    => "src=",
                      "LINK"   => "href=",
                      "AREA"   => "href=",
                      "FRAME"  => "src=",
                      "IFRAME" => "src=",
                      "FORM"   => "action=");

        foreach ($tags as $tag => $key) {
            $prefix = "fetch.php?id=$cm->id&amp;url=";
            if ( $tag == "IMG" or $tag == "LINK" or $tag == "FORM") {
                $prefix = "";
            }
            $client->results = resource_redirect_tags($client->results, $url, $tag, $key,$prefix);
        }
    } else {
        if ( $client->status >= 400 && $client->status < 500) {
            $client->results = get_string("fetchclienterror","resource");  // Client error
        } elseif ( $client->status >= 500 && $client->status < 600) {
            $client->results = get_string("fetchservererror","resource");  // Server error
        } else {
            $client->results = get_string("fetcherror","resource");     // Redirection? HEAD? Unknown error.
        }
    }
    return $client;
}

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

function resource_is_url($path) {
    if (strpos($path, '://')) {     // eg http:// https:// ftp://  etc
        return true;
    }
    if (strpos($path, '/') === 0) { // Starts with slash
        return true;
    }
    return false;
}

function resource_get_types() {
    global $CFG;

    $types = array();

    $standardresources = array('text','html','file','directory');
    foreach ($standardresources as $resourcetype) {
        $type = new object();
        $type->modclass = MOD_CLASS_RESOURCE;
        $type->name = $resourcetype;
        $type->type = "resource&amp;type=$resourcetype";
        $type->typestr = get_string("resourcetype$resourcetype", 'resource');
        $types[] = $type;
    }

    /// Drop-in extra resource types
    $resourcetypes = get_list_of_plugins('mod/resource/type');
    foreach ($resourcetypes as $resourcetype) {
        if (!empty($CFG->{'resource_hide_'.$resourcetype})) {  // Not wanted
            continue;
        }
        if (!in_array($resourcetype, $standardresources)) {
            $type = new object();
            $type->modclass = MOD_CLASS_RESOURCE;
            $type->name = $resourcetype;
            $type->type = "resource&amp;type=$resourcetype";
            $type->typestr = get_string("resourcetype$resourcetype", 'resource');
            $types[] = $type;
        }
    }

    return $types;
}

function resource_get_view_actions() {
    return array('view','view all');
}

function resource_get_post_actions() {
    return array();
}

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
                    if (!$DB->update_record('resource', $r)) {
                        print_error('cannotupdate', 'resource');
                    }
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
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function resource_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 */
function resource_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

require_once($CFG->libdir . '/portfoliolib.php');
class resource_portfolio_caller extends portfolio_module_caller_base {

    private $resource;
    private $resourcefile;

    public function __construct($callbackargs) {
        global $CFG;
        global $DB;
        if (!array_key_exists('id', $callbackargs) || !$this->cm = get_coursemodule_from_instance('resource', $callbackargs['id'])) {
            throw new portfolio_caller_exception('invalidid');
        }
        $this->cm->type = $DB->get_field('resource', 'type', array('id' => $this->cm->instance));
        $resourceclass = 'resource_'. $this->cm->type;
        $this->resourcefile = $CFG->dirroot.'/mod/resource/type/'.$this->cm->type.'/resource.class.php';
        require_once($this->resourcefile);
        $this->resource= new $resourceclass($this->cm->id);
        // this is kind of yuk... but there's just not good enough OO here
        $format = PORTFOLIO_FORMAT_FILE;
        switch ($this->cm->type) {
            case 'html':
                $format = PORTFOLIO_FORMAT_HTML;
            case 'text':
                $format = PORTFOLIO_FORMAT_TEXT;
            case 'file':
                // $format = portfolio_format_from_mimetype($something); // change after we switch upload type resources over to new files api.
        }
    }

    public function __wakeup() {
        global $CFG;
        if (empty($CFG)) {
            return; // too early yet
        }
        require_once($this->resourcefile);
        $this->resource = unserialize(serialize($this->resource));
    }

    public function expected_time() {
        // @todo penny check filesize if the type is uploadey (not implemented yet)
        // like this: return portfolio_expected_time_file($this->file); // or whatever
        return PORTFOLIO_TIME_LOW;
    }

    public function prepare_package() {
        return $this->resource->portfolio_prepare_package($this->exporter);
    }

    public function check_permissions() {
        return has_capability('mod/resource:exportresource', get_context_instance(CONTEXT_MODULE, $this->cm->id));
    }

    public static function add_button($resource, $format=null, $return=false) {
        if (!has_capability('mod/resource:exportresource', get_context_instance(CONTEXT_MODULE, $resource->cm->id))) {
            return;
        }
        if (!is_callable(array($resource, 'portfolio_prepare_package')) || !is_callable(array($resource, 'portfolio_get_sha1'))) {
            debugging(get_string('portfolionotimplemented', 'resource'));
            return false;
        }
        return portfolio_add_button('resource_portfolio_caller', array('id' => $resource->cm->instance),  '/mod/resource/lib.php', $format, null, $return);
    }

    public function get_sha1() {
        return $this->resource->portfolio_get_sha1();
    }

    public static function display_name() {
        return get_string('modulename', 'resource');
    }
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function resource_supports($feature) {
    switch($feature) {
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        default: return null;
    }
}

?>
