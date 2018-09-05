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
 * Functions for component core_tag
 *
 * To set or get item tags refer to the class {@link core_tag_tag}
 *
 * @package    core_tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return a list of page types
 *
 * @package core_tag
 * @param   string   $pagetype       current page type
 * @param   stdClass $parentcontext  Block's parent context
 * @param   stdClass $currentcontext Current context of block
 */
function tag_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return array(
        'tag-*'=>get_string('page-tag-x', 'tag'),
        'tag-index'=>get_string('page-tag-index', 'tag'),
        'tag-search'=>get_string('page-tag-search', 'tag'),
        'tag-manage'=>get_string('page-tag-manage', 'tag')
    );
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function core_tag_inplace_editable($itemtype, $itemid, $newvalue) {
    \external_api::validate_context(context_system::instance());
    if ($itemtype === 'tagname') {
        return \core_tag\output\tagname::update($itemid, $newvalue);
    } else if ($itemtype === 'tagareaenable') {
        return \core_tag\output\tagareaenabled::update($itemid, $newvalue);
    } else if ($itemtype === 'tagareacollection') {
        return \core_tag\output\tagareacollection::update($itemid, $newvalue);
    } else if ($itemtype === 'tagareashowstandard') {
        return \core_tag\output\tagareashowstandard::update($itemid, $newvalue);
    } else if ($itemtype === 'tagcollname') {
        return \core_tag\output\tagcollname::update($itemid, $newvalue);
    } else if ($itemtype === 'tagcollsearchable') {
        return \core_tag\output\tagcollsearchable::update($itemid, $newvalue);
    } else if ($itemtype === 'tagflag') {
        return \core_tag\output\tagflag::update($itemid, $newvalue);
    } else if ($itemtype === 'tagisstandard') {
        return \core_tag\output\tagisstandard::update($itemid, $newvalue);
    }
}
