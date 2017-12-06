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
 * Interface class.
 *
 * @package   mod_dataform
 * @copyright 2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_dataform\interfaces;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for supporting dataformfields which are using a scale
 *
 * It forces inheriting classes to define methods that are called by the dataform
 * for handling scale related operations (e.g. backup).
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface usingscale {

    /**
     * Returns true if any of the dataformfield instances is using the specified scale
     * in the specified dataform or all dataforms.
     *
     * @return bool
     */
    public static function is_using_scale($scaleid, $dataformid = 0);

}
