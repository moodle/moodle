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
 * Block to search forum posts.
 *
 * @package   block_search_forums
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_search_forums extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_search_forums');
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        if (empty($this->instance)) {
            $this->content->text   = '';
            return $this->content;
        }

        $advancedsearch = get_string('advancedsearch', 'block_search_forums');

        $strsearch  = get_string('search');
        $strgo      = get_string('go');

        $this->content->text  = '<div class="searchform">';
        $this->content->text .= '<form action="'.$CFG->wwwroot.'/mod/forum/search.php" style="display:inline"><fieldset class="invisiblefieldset">';
        $this->content->text .= '<legend class="accesshide">'.$strsearch.'</legend>';
        $this->content->text .= '<input name="id" type="hidden" value="'.$this->page->course->id.'" />';  // course
        $this->content->text .= '<label class="accesshide" for="searchform_search">'.$strsearch.'</label>'.
                                '<input id="searchform_search" name="search" type="text" size="16" />';
        $this->content->text .= '<button id="searchform_button" type="submit" title="'.$strsearch.'">'.$strgo.'</button><br />';
        $this->content->text .= '<a href="'.$CFG->wwwroot.'/mod/forum/search.php?id='.$this->page->course->id.'">'.$advancedsearch.'</a>';
        $this->content->text .= $OUTPUT->help_icon('search');
        $this->content->text .= '</fieldset></form></div>';

        return $this->content;
    }

    function applicable_formats() {
        return array('site' => true, 'course' => true);
    }

    /**
     * Returns the role that best describes the forum search block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'search';
    }
}


