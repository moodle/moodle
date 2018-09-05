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
 * Contains class core_tag\output\tagcollname
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\output;

use context_system;
use lang_string;
use html_writer;
use core_tag_collection;
use moodle_url;

/**
 * Class to preapare a tag name for display.
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagcollname extends \core\output\inplace_editable {

    /**
     * Constructor.
     *
     * @param \stdClass $tagcoll
     */
    public function __construct($tagcoll) {
        $editable = has_capability('moodle/tag:manage', context_system::instance());
        $edithint = new lang_string('editcollname', 'core_tag');
        $value = $tagcoll->name;
        $name = \core_tag_collection::display_name($tagcoll);
        $editlabel = new lang_string('newcollnamefor', 'core_tag', $name);
        $manageurl = new moodle_url('/tag/manage.php', array('tc' => $tagcoll->id));
        $displayvalue = html_writer::link($manageurl, $name);
        parent::__construct('core_tag', 'tagcollname', $tagcoll->id, $editable, $displayvalue, $value, $edithint, $editlabel);
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
        \core_tag_collection::update($tagcoll, array('name' => $newvalue));
        return new self($tagcoll);
    }
}
