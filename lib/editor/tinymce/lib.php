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
 * TinyMCE text editor integration.
 *
 * @package    moodlecore
 * @subpackage editor
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class tinymce_texteditor extends texteditor {
    public function supported_by_browser() {
        if (check_browser_version('MSIE', 5.5)) {
            return true;
        } else if (check_browser_version('Gecko', 20030516)) {
            return true;
        } else if (check_browser_version('Safari', 3)) {
            return true;
        } else if (check_browser_version('Opera', 9)) {
            return true;
        }
        
        return true;
    }

    public function get_supported_formats() {
        return array(FORMAT_HTML => FORMAT_HTML);
    }

    public function get_preferred_format() {
        return FORMAT_HTML;
    }

    public function supports_repositories() {
        return true;
    }

    public function get_editor_element_class() {
        return 'form-tinymce-advanced';
    }
    
    public function get_legacy_textarea_class() {
        return 'form-tinymce-legacy';
    }

    public function use_editor($elementid=null) {
        global $CFG, $PAGE;
        $usehttps = (int)($CFG->httpswwwroot !== $CFG->wwwroot); //hmm, is there a better test?

        //TODO: requirements manager does not support parameters :-(

        $PAGE->requires->js($CFG->httpswwwroot.'/lib/editor/tinymce/tiny_mce_src.js', true);
        $PAGE->requires->js($CFG->httpswwwroot.'/lib/editor/tinymce/extra/tinymce.js.php?elanguage='.current_language().'&amp;etheme='.current_theme().'&amp;eusehttps='.$usehttps, true);
    }
    
}