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
