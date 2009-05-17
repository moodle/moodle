<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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

function get_texteditor($editor) {
    global $CFG;

    $libfile = "$CFG->libdir/editor/$editor/lib.php";
    if (!file_exists($libfile)) {
        return false;
    }
    require_once($libfile);
    $classname = $editor.'_texteditor';
    if (!class_exists($classname)) {
        return false;
    }
    return new $classname();
}

 /**
 * Get the list of available editors
 */
function get_available_editors() {
    $editors = array();
    foreach (get_list_of_plugins('lib/editor') as $editor) {
        $editors[$editor] = get_string('modulename', 'editor_'.$editor);
    }
    return $editors;
}

/**
 * Base text editor class
 */
abstract class texteditor {
    public abstract function supported_by_browser();
    public abstract function get_supported_formats();
    public abstract function get_preferred_format();
    public abstract function supports_repositories();
    public abstract function get_editor_element_class();
    public abstract function get_legacy_textarea_class();
    public abstract function header_js();
}



//=== DEPRECATED =====================
/**
 * Deprecated...
 */
function can_use_html_editor() {
    //TODO: eradicate completely

    return true;
}
