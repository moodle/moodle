<?php

/**
 *
 * @copyright &copy; 2006 The Open University
 * @author d.t.le@open.ac.uk, a.j.forth@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package portfolio
 */


class htmlEditor {

    public function __construct() {
    }

    /**
     * Does initial configuration for a given html editor.
     * @param string editor the name of desired html editor, system default will be used if none is passed
     * @param int courseid the courseid uploaded files should be linked to
     * @return bool true if an editor was configured, false otherwise.
     */
    public function configure($editor = NULL, $courseid = NULL) {

        global $CFG;
        static $configured = Array();

        if (!isset($CFG->htmleditor) or (!$CFG->htmleditor)) {
            return;
        }

        if ($editor == '') {
            $editor = (isset($CFG->defaulthtmleditor) ? $CFG->defaulthtmleditor : '');
        }

        if (isset($configured[$editor])) {
            return $configured[$editor];
        }

        $configuration = array();

        switch ($editor) {

            case 'tinymce':
                $editorlanguage = current_language();
                $configuration[] = $CFG->httpswwwroot ."/lib/editor/tinymce/jscripts/tiny_mce/tiny_mce.js";
                //$configuration[] = $CFG->httpswwwroot ."/lib/editor/tinymce/jscripts/tiny_mce/tiny_mce_src.js";
                $configuration[] = $CFG->httpswwwroot ."/lib/editor/tinymce/tinymce.js.php?course=$courseid&amp;editorlanguage=$editorlanguage";
                $configured['tinymce'] = true;
                break;

            default:
                $configured[$editor] = false;
                break;

        }

        if (isset($CFG->editorsrc) && is_array($CFG->editorsrc)) {
            $CFG->editorsrc = $configuration + $CFG->editorsrc;
        } else {
            $CFG->editorsrc = $configuration;
        }

        return $configured[$editor];
    }
}

?>
