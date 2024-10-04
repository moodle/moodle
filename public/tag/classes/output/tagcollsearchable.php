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
 * Contains class core_tag\output\tagcollsearchable
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\output;

use context_system;
use lang_string;
use core_tag_collection;

/**
 * Class to display tag collection searchable control
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagcollsearchable extends \core\output\inplace_editable {

    /**
     * Constructor.
     *
     * @param \stdClass $tagcoll
     */
    public function __construct($tagcoll) {
        $editable = has_capability('moodle/tag:manage', context_system::instance());
        $edithint = new lang_string('editsearchable', 'core_tag');
        $value = $tagcoll->searchable ? 1 : 0;

        parent::__construct('core_tag', 'tagcollsearchable', $tagcoll->id, $editable, $value, $value, $edithint);
        $this->set_type_toggle();
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {
        if ($this->value) {
            $this->displayvalue = $output->pix_icon('i/checked', get_string('yes'));
        } else {
            $this->displayvalue = $output->pix_icon('i/unchecked', get_string('no'));
        }

        return parent::export_for_template($output);
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
        $tagcoll = $DB->get_record('tag_coll', array('id' => $itemid), '*', MUST_EXIST);
        core_tag_collection::update($tagcoll, array('searchable' => $newvalue));
        return new self($tagcoll);
    }
}
