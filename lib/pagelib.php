<?php //$Id$

define('MOODLE_PAGE_COURSE',    'course');

/**
 * Parent class from which all Moodle page classes derive
 *
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @author Jon Papaioannou
 * @package pages
 */

// This is very messy still. Please for the moment ignore it [except maybe create_object()]
// and move on to the derived class MoodlePage_Course to see the comments there.
class MoodlePage {
    var $type           = NULL;
    var $id             = NULL;
    var $full_init_done = false;

    function blocks_get_positions() {
        return array();
    }
    function url_get_path() {
        return NULL;
    }
    function url_get_parameters() {
        return array();
    }
    function url_get_full($extraparams = array()) {
        $path = $this->url_get_path();
        if(empty($path)) {
            return NULL;
        }

        $params = $this->url_get_parameters();
        $params = array_merge($params, $extraparams);

        if(empty($params)) {
            return $path;
        }
        
        $first = true;

        foreach($params as $var => $value) {
            $path .= $first? '?' : '&amp;';
            $path .= $var.'='.urlencode($value);
            $first = false;
        }

        return $path;
    }
    function get_type() {
        error('get_type() called on a page object which is not correctly implemented');
    }
    function get_id() {
        return $this->id;
    }
    function get_format_name() {
        return NULL;
    }
    function user_allowed_editing() {
        return false;
    }
    function user_is_editing() {
        return false;
    }

    function init_quick($data) {
        $this->type = $data->pagetype;
        $this->id   = $data->pageid;
    }

    function create_object($type, $id = NULL) {

        $data = new stdClass;
        $data->pagetype = $type;
        $data->pageid   = $id;

        // This might be moved somewhere more easily accessible from the outside,
        // as anyone that implements a new Page class will need to add a line here.
        $typeids = array(
            MOODLE_PAGE_COURSE => 'MoodlePage_Course'
        );

        if(!isset($typeids[$type])) {
            error('Unrecognized type passed to MoodlePage::create_object: '.$type);
        }

        $object = &new $typeids[$type];

        if($object->get_type() !== $type) {
            // Somehow somewhere someone made a mistake
            error("Page object's type (".$object->get_type().") does not match requested type ($type)");
        }

        $object->init_quick($data);
        return $object;
    }
}


/**
 * Class that models the behavior of a moodle course
 *
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @author Jon Papaioannou
 * @package pages
 */

class MoodlePage_Course extends MoodlePage {

    // Any data we might need to store specifically about ourself should be declared here.
    // After init_full() is called for the first time, ALL of these variables should be
    // initialized correctly and ready for use.
    var $courserecord = NULL;

    // Do any validation of the officially recognized bits of the data and forward to parent.
    // Do NOT load up "expensive" resouces (e.g. SQL data) here!
    function init_quick($data) {
        if(empty($data->pageid)) {
            error('Cannot quickly initialize page: empty course id ');
        }
        parent::init_quick($data);
    }

    // Here you should load up all heavy-duty data for your course. Basically everything that
    // does not NEED to be loaded for the class to make basic decisions should NOT be loaded
    // in init_quick() and instead deferred here. Of course this function had better recognize
    // $this->full_init_done to prevent wasteful multiple-time data retrieval.
    function init_full() {
        if($this->full_init_done) {
            return;
        }
        $this->courserecord = get_record('course', 'id', $this->id);
        if(empty($this->courserecord)) {
            error('Cannot fully initialize page: invalid course id '.$this->id);
        }
        $this->full_init_done = true;
    }

    // When is a user said to have "editing rights" in this page? This would have something
    // to do with roles, in the future.
    function user_allowed_editing() {
        return isteacheredit($this->id);
    }

    // Is the user actually editing this page right now? This would have something
    // to do with roles, in the future.
    function user_is_editing() {
        return isediting($this->id);
    }

    // HTML output section. This function prints out the common part of the page's header.
    // You should NEVER print the header "by hand" in other code.
    function print_header($title) {
        global $USER;
        $this->init_full();
        $replacements = array(
            '%fullname%' => $this->courserecord->fullname
        );
        foreach($replacements as $search => $replace) {
            $title = str_replace($search, $replace, $title);
        }

        $loggedinas = '<p class="logininfo">'. user_login_string($this->courserecord, $USER) .'</p>';
        print_header($title, $this->courserecord->fullname, $this->courserecord->shortname,
                     '', '', true, update_course_icon($this->courserecord->id), $loggedinas);
    }

    // This is hardwired here so the factory method create_object() can be sure there was no mistake.
    // Also, it doubles as a way to let others inquire about our type.
    function get_type() {
        return MOODLE_PAGE_COURSE;
    }

    // This is like the "category" of a page of this "type". For example, if the type is MOODLE_PAGE_COURSE
    // the format_name is the actual name of the course format. If the type were MOODLE_PAGE_ACTIVITY, then
    // the format_name might be that activity's name etc.
    function get_format_name() {
        $this->init_full();
        return $this->courserecord->format;
    }

    // This should return a fully qualified path to the URL which is responsible for displaying us.
    function url_get_path() {
        global $CFG;
        if($this->id == SITEID) {
            return $CFG->wwwroot.'/index.php';
        }
        else {
            return $CFG->wwwroot.'/course/view.php';
        }
    }

    // This should return an associative array of any GET/POST parameters that are needed by the URL
    // which displays us to make it work. If none are needed, return an empty array.
    function url_get_parameters() {
        if($this->id == SITEID) {
            return array();
        }
        else {
            return array('id' => $this->id);
        }
    }

    // Blocks-related section

    // Which are the positions in this page which support blocks? Return an array containing their identifiers.
    // BE CAREFUL, ORDER DOES MATTER! In textual representations, lists of blocks in a page use the ':' character
    // to delimit different positions in the page. The part before the first ':' in such a representation will map
    // directly to the first item of the array you return here, the second to the next one and so on. This way,
    // you can add more positions in the future without interfering with legacy textual representations.
    function blocks_get_positions() {
        return array(BLOCK_POS_LEFT, BLOCK_POS_RIGHT);
    }

    // When a new block is created in this page, which position should it go to?
    function blocks_default_position() {
        return BLOCK_POS_RIGHT;
    }

    // When we are creating a new page, use the data at your disposal to provide a textual representation of the
    // blocks that are going to get added to this new page. Delimit block names with commas (,) and use double
    // colons (:) to delimit between block positions in the page. See blocks_get_positions() for additional info.
    function blocks_get_default() {
        global $CFG;
        
        $this->init_full();

        if($this->id == SITEID) {
        // Is it the site?
            if (!empty($CFG->defaultblocks_site)) {
                $blocknames = $CFG->defaultblocks_site;
            }
            /// Failsafe - in case nothing was defined.
            else {
                $blocknames = 'site_main_menu,admin,course_list:course_summary,calendar_month';
            }
        }
        // It's a normal course, so do it according to the course format
        else {
            $pageformat = $this->courserecord->format;
            if (!empty($CFG->{'defaultblocks_'. $pageformat})) {
                $blocknames = $CFG->{'defaultblocks_'. $pageformat};
            }
            else {
                $format_config = $CFG->dirroot.'/course/format/'.$pageformat.'/config.php';
                if (@is_file($format_config) && is_readable($format_config)) {
                    require($format_config);
                }
                if (!empty($format['defaultblocks'])) {
                    $blocknames = $format['defaultblocks'];
                }
                else if (!empty($CFG->defaultblocks)){
                    $blocknames = $CFG->defaultblocks;
                }
                /// Failsafe - in case nothing was defined.
                else {
                    $blocknames = 'participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity';
                }
            }
        }
        
        return $blocknames;
    }

    // Given an instance of a block in this page and the direction in which we want to move it, where is
    // it going to go? Return the identifier of the instance's new position. This allows us to tell blocklib
    // how we want the blocks to move around in this page in an arbitrarily complex way. If the move as given
    // does not make sense, make sure to return the instance's original position.
    //
    // Since this is going to get called a LOT, pass the instance by reference purely for speed. Do **NOT**
    // modify its data in any way, this will actually confuse blocklib!!!
    function blocks_move_position(&$instance, $move) {
        if($instance->position == BLOCK_POS_LEFT && $move == BLOCK_MOVE_RIGHT) {
            return BLOCK_POS_RIGHT;
        }
        else if ($instance->position == BLOCK_POS_RIGHT && $move == BLOCK_MOVE_LEFT) {
            return BLOCK_POS_LEFT;
        }
        return $instance->position;
    }
}

?>
