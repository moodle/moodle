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
 * Contains class core_tag\output\tagareacollection
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\output;

use context_system;
use lang_string;
use core_tag_area;

/**
 * Class to display collection select for the tag area
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagareacollection extends \core\output\inplace_editable {

    /**
     * Constructor.
     *
     * @param \stdClass $tagarea
     */
    public function __construct($tagarea) {
        if (!empty($tagarea->locked)) {
            // If the tag collection for the current tag area is locked, display the
            // name of the collection without possibility to edit it.
            $tagcoll = \core_tag_collection::get_by_id($tagarea->tagcollid);
            parent::__construct('core_tag', 'tagareacollection', $tagarea->id, false,
                \core_tag_collection::display_name($tagcoll), $tagarea->tagcollid);
            return;
        }

        $tagcollections = \core_tag_collection::get_collections_menu(true);
        $editable = (count($tagcollections) > 1) &&
                has_capability('moodle/tag:manage', context_system::instance());
        $areaname = core_tag_area::display_name($tagarea->component, $tagarea->itemtype);
        $edithint = new lang_string('edittagcollection', 'core_tag');
        $editlabel = new lang_string('changetagcoll', 'core_tag', $areaname);
        $value = $tagarea->tagcollid;

        parent::__construct('core_tag', 'tagareacollection', $tagarea->id, $editable,
                null, $value, $edithint, $editlabel);
        $this->set_type_select($tagcollections);
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
        require_capability('moodle/tag:manage', \context_system::instance());
        $tagarea = $DB->get_record('tag_area', array('id' => $itemid), '*', MUST_EXIST);
        $newvalue = clean_param($newvalue, PARAM_INT);
        $tagcollections = \core_tag_collection::get_collections_menu(true);
        if (!array_key_exists($newvalue, $tagcollections)) {
            throw new \moodle_exception('invalidparameter', 'debug');
        }
        $data = array('tagcollid' => $newvalue);
        core_tag_area::update($tagarea, $data);
        $tagarea->tagcollid = $newvalue;
        $tmpl = new self($tagarea);
        return $tmpl;
    }
}
