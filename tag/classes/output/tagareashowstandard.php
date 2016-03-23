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
 * Contains class core_tag\output\tagareashowstandard
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\output;

use context_system;
use lang_string;
use core_tag_tag;
use core_tag_area;

/**
 * Class to display tag area show standard control
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagareashowstandard extends \core\output\inplace_editable {

    /**
     * Constructor.
     *
     * @param \stdClass $tagarea
     */
    public function __construct($tagarea) {
        $editable = has_capability('moodle/tag:manage', context_system::instance());
        $edithint = new lang_string('editisstandard', 'core_tag');
        $value = $tagarea->showstandard;
        $areaname = core_tag_area::display_name($tagarea->component, $tagarea->itemtype);
        $editlabel = new lang_string('changeshowstandard', 'core_tag', $areaname);

        parent::__construct('core_tag', 'tagareashowstandard', $tagarea->id, $editable,
                null, $value, $edithint, $editlabel);

        $standardchoices = array(
            core_tag_tag::BOTH_STANDARD_AND_NOT => get_string('standardsuggest', 'tag'),
            core_tag_tag::STANDARD_ONLY => get_string('standardforce', 'tag'),
            core_tag_tag::HIDE_STANDARD => get_string('standardhide', 'tag')
        );
        $this->set_type_select($standardchoices);
    }

    /**
     * Updates the value in database and returns itself, called from inplace_editable callback
     *
     * @param int $itemid
     * @param mixed $newvalue
     * @return \self
     */
    public static function update($itemid, $newvalue) {
        global $DB;
        require_capability('moodle/tag:manage', context_system::instance());
        $tagarea = $DB->get_record('tag_area', array('id' => $itemid), '*', MUST_EXIST);
        $newvalue = clean_param($newvalue, PARAM_INT);
        $data = array('showstandard' => $newvalue);
        core_tag_area::update($tagarea, $data);
        $tagarea->showstandard = $newvalue;
        $tmpl = new self($tagarea);
        return $tmpl;
    }
}
