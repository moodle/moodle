<?php // $Id$
/**
 * Page class for lesson
 *
 * @author Mark Nielsen
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->dirroot.'/course/lib.php'); // needed for some blocks

/**
 * Define the page types
 *
 **/
define('PAGE_LESSON_VIEW', 'mod-lesson-view');

/**
 * Map the classes to the page types
 *
 **/
page_map_class(PAGE_LESSON_VIEW, 'page_lesson');

/**
 * Add the page types defined in this file
 *
 **/
$DEFINEDPAGES = array(PAGE_LESSON_VIEW);

/**
 * Class that models the behavior of a lesson
 *
 * @author Mark Nielsen (lesson extention only)
 * @package lesson
 **/
class page_lesson extends page_generic_activity {

    /**
     * Module name
     *
     * @var string
     **/
    var $activityname = 'lesson';
    /**
     * Current lesson page ID
     *
     * @var string
     **/
    var $lessonpageid = NULL;

    /**
     * Print a standard lesson heading.
     *
     * This will also print up to three
     * buttons in the breadcrumb, lesson heading
     * lesson tabs, lesson notifications and perhaps
     * a popup with a media file.
     *
     * @return void
     **/
    function print_header($title = '', $morenavlinks = array()) {
        global $CFG;

        $this->init_full();

    /// Variable setup/check
        $context      = get_context_instance(CONTEXT_MODULE, $this->modulerecord->id);
        $activityname = format_string($this->activityrecord->name);

        if ($this->lessonpageid === NULL) {
            error('Programmer error: must set the lesson page ID');
        }
        if (empty($title)) {
            $title = "{$this->courserecord->shortname}: $activityname";
        }
        
    /// Build the buttons
        if (has_capability('mod/lesson:edit', $context)) {
            $buttons = '<span class="edit_buttons">'.update_module_button($this->modulerecord->id, $this->courserecord->id, get_string('modulename', 'lesson'));

            if (!empty($this->lessonpageid) and $this->lessonpageid != LESSON_EOL) {
                $buttons .= '<form '.$CFG->frametarget.' method="get" action="'.$CFG->wwwroot.'/mod/lesson/lesson.php">'.
                            '<input type="hidden" name="id" value="'.$this->modulerecord->id.'" />'.
                            '<input type="hidden" name="action" value="editpage" />'.
                            '<input type="hidden" name="redirect" value="navigation" />'.
                            '<input type="hidden" name="pageid" value="'.$this->lessonpageid.'" />'.
                            '<input type="submit" value="'.get_string('editpagecontent', 'lesson').'" />'.
                            '</form>';

                if (!empty($CFG->showblocksonmodpages) and $this->user_allowed_editing()) {
                    if ($this->user_is_editing()) {
                        $onoff = 'off';
                    } else {
                        $onoff = 'on';
                    }
                    $buttons .= '<form '.$CFG->frametarget.' method="get" action="'.$CFG->wwwroot.'/mod/lesson/view.php">'.
                                '<input type="hidden" name="id" value="'.$this->modulerecord->id.'" />'.
                                '<input type="hidden" name="pageid" value="'.$this->lessonpageid.'" />'.
                                '<input type="hidden" name="edit" value="'.$onoff.'" />'.
                                '<input type="submit" value="'.get_string("blocksedit$onoff").'" />
                                </form>';
                }
            }
            $buttons .= '</span>';
        } else {
            $buttons = '&nbsp;';
        }

    /// Build the meta
    /// Currently broken because the $meta is printed before the JavaScript is printed
        // if (!optional_param('pageid', 0, PARAM_INT) and !empty($this->activityrecord->mediafile)) {
        //     // open our pop-up
        //     $url = '/mod/lesson/mediafile.php?id='.$this->modulerecord->id;
        //     $name = 'lessonmediafile';
        //     $options = 'menubar=0,location=0,left=5,top=5,scrollbars,resizable,width='. $this->activityrecord->mediawidth .',height='. $this->activityrecord->mediaheight;
        //     $meta = "\n<script type=\"text/javascript\">";
        //     $meta .= "\n<!--\n";
        //     $meta .= "     openpopup('$url', '$name', '$options', 0);";
        //     $meta .= "\n// -->\n";
        //     $meta .= '</script>';
        // } else {
            $meta = '';
        // }

        $navigation = build_navigation($morenavlinks, $this->modulerecord);
        print_header($title, $this->courserecord->fullname, $navigation, '', $meta, true, $buttons, navmenu($this->courserecord, $this->modulerecord));

        if (has_capability('mod/lesson:manage', $context)) {
            print_heading_with_help($activityname, 'overview', 'lesson');

            // Rename our objects for the sake of the tab code
            list($cm, $course, $lesson, $currenttab) = array(&$this->modulerecord, &$this->courserecord, &$this->activityrecord, 'view');
            include($CFG->dirroot.'/mod/lesson/tabs.php');
        } else {
            print_heading($activityname);
        }

        lesson_print_messages();
    }

    function get_type() {
        return PAGE_LESSON_VIEW;
    }

    function blocks_get_positions() {
        return array(BLOCK_POS_LEFT, BLOCK_POS_RIGHT);
    }

    function blocks_default_position() {
        return BLOCK_POS_RIGHT;
    }

    function blocks_move_position(&$instance, $move) {
        if($instance->position == BLOCK_POS_LEFT && $move == BLOCK_MOVE_RIGHT) {
            return BLOCK_POS_RIGHT;
        } else if ($instance->position == BLOCK_POS_RIGHT && $move == BLOCK_MOVE_LEFT) {
            return BLOCK_POS_LEFT;
        }
        return $instance->position;
    }

    /**
     * Needed to add the ID of the current lesson page
     *
     * @return array
     **/
    function url_get_parameters() {
        $this->init_full();
        return array('id' => $this->modulerecord->id, 'pageid' => $this->lessonpageid);;
    }

    /**
     * Set the current lesson page ID
     *
     * @return void
     **/
    function set_lessonpageid($pageid) {
        $this->lessonpageid = $pageid;
    }
}
?>
