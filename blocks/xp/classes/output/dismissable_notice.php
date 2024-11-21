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
 * Dismissable notice.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\output;

/**
 * Dismissable notice.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dismissable_notice extends notice {

    /** @var string The name. */
    public $name;

    /**
     * Constructor.
     *
     * @param string $message The message.
     * @param string $name The name.
     * @param string $type The type.
     */
    public function __construct($message, $name, $type = self::INFO) {
        if (!preg_match('/^[a-z0-9_-]+$/', $name)) {
            throw new \coding_exception('Invalid name for a notice.');
        }
        parent::__construct($message, $type);
        $this->name = $name;
    }

}
