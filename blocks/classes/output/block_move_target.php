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

namespace core_block\output;

use moodle_url;

/**
 * This class represents a target for where a block can go when it is being moved.
 *
 * This needs to be rendered as a form with the given hidden from fields, and
 * clicking anywhere in the form should submit it. The form action should be
 * $PAGE->url.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core_block
 * @category output
 */
class block_move_target {
    /**
     * Constructor.
     *
     * @param moodle_url $url
     */
    public function __construct(
        /** @var moodle_url Move url */
        public moodle_url $url,
    ) {
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(block_move_target::class, \block_move_target::class);
