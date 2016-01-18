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
 * Contains class core_tag\output\tagname
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\output;

use context_system;
use lang_string;
use html_writer;
use core_tag_tag;

/**
 * Class to preapare a tag name for display.
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tagname extends \core\output\inplace_editable {

    /**
     * Constructor.
     *
     * @param stdClass|core_tag $tag
     */
    public function __construct($tag) {
        $editable = has_capability('moodle/tag:manage', context_system::instance());
        $edithint = new lang_string('editname', 'core_tag');
        $editlabel = new lang_string('newnamefor', 'core_tag', $tag->rawname);
        $value = $tag->rawname;
        $displayvalue = html_writer::link(core_tag_tag::make_url($tag->tagcollid, $tag->rawname),
            core_tag_tag::make_display_name($tag));
        parent::__construct('core_tag', 'tagname', $tag->id, $editable, $displayvalue, $value, $edithint, $editlabel);
    }
}
