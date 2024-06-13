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

use core\output\pix_icon;
use moodle_url;

/**
 * A primary action menu action
 *
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class link_primary extends link {
    /**
     * Constructs the object.
     *
     * @param moodle_url $url
     * @param pix_icon|null $icon
     * @param string $text
     * @param array $attributes
     */
    public function __construct(moodle_url $url, ?pix_icon $icon, $text, array $attributes = []) {
        parent::__construct($url, $icon, $text, true, $attributes);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(link_primary::class, \action_menu_link_primary::class);
