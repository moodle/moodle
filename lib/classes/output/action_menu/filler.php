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

namespace core\output\action_menu;

use core\output\action_link;
use core\output\html_writer;
use core\output\renderable;

/**
 * An action menu filler
 *
 * @package core
 * @category output
 * @copyright 2013 Andrew Nicols
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filler extends action_link implements renderable {
    /**
     * True if this is a primary action. False if not.
     * @var bool
     */
    public $primary = true;

    /**
     * Constructs the object.
     */
    public function __construct() {
        $this->attributes['id'] = html_writer::random_id('action_link');
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(filler::class, \action_menu_filler::class);
