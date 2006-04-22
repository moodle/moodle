<?php // $Id$

/**
 * Editor class for Moodle.
 *
 * This library is made to make easier to intergrate
 * WYSIWYG editors into Moodle.
 *
 * @author Janne Mikkonen
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package editorObject
 */
class editorObject {

    /**
    * Holds the internal $USER standard object.
    * @var object $user
    */
    var $user;

    /**
    * Holds the course id.
    * @var int $courseid
    */
    var $courseid;

    /**
    * Hold the internal $CFG standard class.
    * @var object $cfg
    */
    var $cfg;

    /**
    * PHP4 style class constructor.
    *
    * @uses $CFG, $USER
    */
    function editorObject () {
        global $CFG, $USER;
        $this->cfg  = &$CFG;
        $this->user = &$USER;
        $this->courseid = NULL;
    }

    /**
    * PHP5 style class constructor.
    *
    */
    function __construct() {
        $this->editorObject();
    }

    /**
    * Method to load necessary editor codes to
    * $CFG->editorsrc array.
    *
    * @todo example code.
    * @param mixed $args Course id or associative array holding course id and editor name.
    */
    function loadeditor($args) {

        global $CFG, $USER;

        if ( is_array($args) ) {
            // If args is an array search keys courseid and name.
            // Name represents editor name to load.
            if ( !array_key_exists('courseid', $args) ) {
                error("Required variable courseid is missing!");
            }

            if ( !array_key_exists('name', $args) ) {
                error("Required variable name is missing!");
            }

            $courseid   = clean_param($args['courseid'], PARAM_INT);
            $editorname = strtolower(clean_param($args['name'], PARAM_ALPHA));
        } else {
            // If only single argument is passed
            // this must be course id.
            $courseid = clean_param($args, PARAM_INT);
        }

        $htmleditor = !empty($editorname) ? $editorname : intval($USER->htmleditor);

        if ( can_use_html_editor() ) {
            $CFG->editorsrc = array();
            $editorbaseurl = $CFG->httpswwwroot .'/lib/editor';
            $editorbasedir = $CFG->dirroot .'/lib/editor';

            switch ($htmleditor) {
                case 1:
                case 'htmlarea':
                    array_push($CFG->editorsrc, "$editorbaseurl/htmlarea/htmlarea.php?id={$courseid}");
                    array_push($CFG->editorsrc, "$editorbaseurl/htmlarea/lang/en.php");
                    $classfile = "$editorbasedir/htmlarea/htmlarea.class.php";
                    include_once($classfile);
                    return (new htmlarea($courseid));
                break;
                case 2:
                case 'tinymce':
                    array_push($CFG->editorsrc, "$editorbaseurl/tinymce/jscripts/tiny_mce/tiny_mce_gzip.php");
                    array_push($CFG->editorsrc, "$editorbaseurl/tinymce/moodledialog.js");
                    $classfile = "$editorbasedir/tinymce/tinymce.class.php";
                    include_once($classfile);
                    return (new tinymce($courseid));
                break;
            }

        }

    }

    /**
    * Print out error message and stop outputting.
    *
    * @param string $message
    */
    function error($message) {
        echo '<div style="text-align: center; font-weight: bold; color: red;">';
        echo '<span style="color: black;">editorObject error:</span> ';
        echo s($message, true);
        echo '</div>';
        exit;
    }

}
?>