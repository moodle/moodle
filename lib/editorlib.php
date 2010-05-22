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
 * Utility classes and functions for text editor integration.
 *
 * @package    moodlecore
 * @subpackage editor
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns users preferred editor for given format
 *
 * @todo  implement user preferences for text editors
 *
 * @global object
 * @global object
 * @param int $format text format or null of none
 * @return texteditor object
 */
function get_preferred_texteditor($format=null) {
    global $CFG, $USER;

    if (empty($CFG->texteditors)) {
        $CFG->texteditors = 'tinymce,textarea';
    }
    $active = explode(',', $CFG->texteditors);

    // TODO: implement user preferences for text editors

    // now find some plugin that supports format and is available
    $editor = false;
    foreach ($active as $editorname) {
        if (!$e = get_texteditor($editorname)) {
            continue;
        }
        if (!$e->supported_by_browser()) {
            // bad luck, this editor is not compatible
            continue;
        }
        if (!$supports = $e->get_supported_formats()) {
            continue;
        }
        if (is_null($format)) {
            // format does not matter
            $editor = $e;
            break;
        }
        if (in_array($format, $supports)) {
            // editor supports this format, yay!
            $editor = $e;
            break;
        }
    }

    if (!$editor) {
        $editor = get_texteditor('textarea'); // must exist and can edit anything
    }

    return $editor;
}

/**
 * Returns instance of text editor
 *
 * @global object
 * @param string $editorname name of editor (textarea, tinymce, ...)
 * @return object|bool texeditor instance or false if does not exist
 */
function get_texteditor($editorname) {
    global $CFG;

    $libfile = "$CFG->libdir/editor/$editorname/lib.php";
    if (!file_exists($libfile)) {
        return false;
    }
    require_once($libfile);
    $classname = $editorname.'_texteditor';
    if (!class_exists($classname)) {
        return false;
    }
    return new $classname();
}

/**
 * Get the list of available editors
 *
 * @return array Array ('editorname'=>'localised editor name')
 */
function get_available_editors() {
    $editors = array();
    foreach (get_plugin_list('editor') as $editorname => $dir) {
        $editors[$editorname] = get_string('modulename', 'editor_'.$editorname);
    }
    return $editors;
}

/**
 * Setup all JS and CSS needed for editors.
 * @return void
 */
function editors_head_setup() {
    global $CFG;

    if (empty($CFG->texteditors)) {
        $CFG->texteditors = 'tinymce,textarea';
    }
    $active = explode(',', $CFG->texteditors);

    foreach ($active as $editorname) {
        if (!$editor = get_texteditor($editorname)) {
            continue;
        }
        if (!$editor->supported_by_browser()) {
            // bad luck, this editor is not compatible
            continue;
        }
        $editor->head_setup();
    }
}

/**
 * Base abstract text editor class.
 *
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
abstract class texteditor {
    /**
     * Is editor supported in current browser?
     * @return bool
     */
    public abstract function supported_by_browser();

    /**
     * Returns list of supported text formats
     * @return array Array (FORMAT=>FORMAT)
     */
    public abstract function get_supported_formats();

    /**
     * Returns main preferred text format.
     * @return int text format
     */
    public abstract function get_preferred_format();

    /**
     * Supports file picker and repos?
     * @return object book object
     */
    public abstract function supports_repositories();

    /**
     * Add required JS needed for editor
     * @param string $elementid id of text area to be converted to editor
     * @param array $options
     * @param obejct $fpoptions file picker options
     * @return void
     */
    public abstract function use_editor($elementid, array $options=null, $fpoptions = null);

    /**
     * Setup all JS and CSS needed for editor.
     * @return void
     */
    public function head_setup() {
    }
}

//TODO: this is very wrong way to do admin settings - this has to be rewritten
require_once($CFG->libdir.'/formslib.php'); 
/**
 * Editor settings moodle form class.
 *
 * @copyright  2010 Dongsheng Cai
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class editor_settings_form extends moodleform {
    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('hidden', 'action', 'edit');
        $this->add_action_buttons(true, get_string('savechanges'));
    }
}

//=== DEPRECATED =====================
/**
 * can_use_html_editor is deprecated...
 * @deprecated
 * @todo Deprecated: eradicate completely, replace with something else
 * @return bool
 */
function can_use_html_editor() {
    //TODO: eradicate completely, replace with something else

    $tinymyce = get_texteditor('tinymce');
    return $tinymyce ->supported_by_browser();
}
