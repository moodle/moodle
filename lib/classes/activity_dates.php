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
 * Contains the base class for fetching the important dates in an activity module for a given module instance and a user.
 *
 * @package   core
 * @copyright Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core;

use cm_info;

/**
 * Class for fetching the  important dates of an activity module for a given module instance and a user.
 *
 * @copyright Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activity_dates {

    /**
     * @var cm_info The course module information object.
     */
    protected $cm;

    /**
     * @var int The user id.
     */
    protected $userid;

    /**
     * activity_dates constructor.
     *
     * @param cm_info $cm course module
     * @param int $userid user id
     */
    public function __construct(cm_info $cm, int $userid) {
        $this->cm = $cm;
        $this->userid = $userid;
    }

    /**
     * Returns a list of important dates in the given module for the user.
     *
     * @param cm_info $cm The course module information.
     * @param int $userid The user ID.
     * @return array|array[]
     */
    public static function get_dates_for_module(cm_info $cm, int $userid): array {
        $cmdatesclassname = static::get_dates_classname($cm->modname);
        if (!$cmdatesclassname) {
            return [];
        }

        /** @var activity_dates $dates */
        $dates = new $cmdatesclassname($cm, $userid);
        return $dates->get_dates();
    }

    /**
     * Fetches the module's dates class implementation if it's available.
     *
     * @param string $modname The activity module name. Usually from cm_info::modname.
     * @return string|null
     */
    private static function get_dates_classname(string $modname): ?string {
        $cmdatesclass = "mod_{$modname}\\dates";
        if (class_exists($cmdatesclass) && is_subclass_of($cmdatesclass, self::class)) {
            return $cmdatesclass;
        }

        return null;
    }

    /**
     * Returns a list of important dates for this module.
     *
     * @return array[] Each element of the array is an array with keys:
     *                 label - The label for the date
     *                 timestamp - The date
     */
    protected abstract function get_dates(): array;
}
