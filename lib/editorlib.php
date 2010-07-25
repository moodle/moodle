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
 * @package    core
 * @subpackage editor
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns users preferred editor for given format
 *
 * @param int $format text format or null of none
 * @return texteditor object
 */
function editors_get_preferred_editor($format = NULL) {
    global $USER;

    $enabled = editors_get_enabled();

    $preventhtml = (count($enabled) > 1 and empty($USER->htmleditor));

    // now find some plugin that supports format and is available
    $editor = false;
    foreach ($enabled as $e) {
        if (!$e->supported_by_browser()) {
            // bad luck, this editor is not compatible
            continue;
        }
        if ($preventhtml and $format == FORMAT_HTML and $e->get_preferred_format() == FORMAT_HTML) {
            // this is really not what we want but we could use it if nothing better found
            $editor = $e;
            continue;
        }
        if (!$supports = $e->get_supported_formats()) {
            // buggy editor!
            continue;
        }
        if (is_null($format)) {
            // format does not matter
            if ($preventhtml and $e->get_preferred_format() == FORMAT_HTML) {
                // this is really not what we want but we could use it if nothing better found
                $editor = $e;
                continue;
            } else {
                $editor = $e;
                break;
            }
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
 * Returns users preferred text format.
 * @return int standard text format
 */
function editors_get_preferred_format() {
    global $USER;

    $editors = editors_get_enabled();
    if (count($editors) == 1) {
        $editor = reset($editors);
        return $editor->get_preferred_format();
    }

    foreach ($editors as $editor) {
        if (empty($USER->htmleditor) and $editor->get_preferred_format() == FORMAT_HTML) {
            // we do not prefer this one
            continue;
        }
        return $editor->get_preferred_format();
    }

    // user did not want html editor, but there is no other choice, sorry
    $editor = reset($editors);
    return $editor->get_preferred_format();
}

/**
 * Returns list of enabled text editors
 * @return array of name=>texteditor
 */
function editors_get_enabled() {
    global $CFG;

    if (empty($CFG->texteditors)) {
        $CFG->texteditors = 'tinymce,textarea';
    }
    $active = array();
    foreach(explode(',', $CFG->texteditors) as $e) {
        if ($editor = get_texteditor($e)) {
            $active[$e] = $editor;
        }
    }

    if (empty($active)) {
        return array('textarea'=>get_texteditor('textarea')); // must exist and can edit anything
    }

    return $active;
}

/**
 * Returns instance of text editor
 *
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
function editors_get_available() {
    $editors = array();
    foreach (get_plugin_list('editor') as $editorname => $dir) {
        $editors[$editorname] = get_string('pluginname', 'editor_'.$editorname);
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

//=== TO BE DEPRECATED in 2.1 =====================

/**
 * Does the user want and can edit using rich text html editor?
 * @todo Deprecate: eradicate completely, replace with something else in the future
 * @return bool
 */
function can_use_html_editor() {
    global $USER;

    $editors = editors_get_enabled();
    if (count($editors) > 1) {
        if (empty($USER->htmleditor)) {
            return false;
        }
    }

    foreach ($editors as $editor) {
        if ($editor->get_preferred_format() == FORMAT_HTML) {
            return true;
        }
    }

    return false;
}
