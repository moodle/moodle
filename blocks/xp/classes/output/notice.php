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
 * Notice.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\output;

use renderable;

/**
 * Notice.
 *
 * We cannot use the core\output\notification class in older versions.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notice implements renderable {

    /** @var string Success. */
    const SUCCESS = 'success';
    /** @var string warning. */
    const WARNING = 'warning';
    /** @var string info. */
    const INFO = 'info';
    /** @var string error. */
    const ERROR = 'error';

    /** @var string The message. */
    public $message;
    /** @var string The name. */
    public $name;
    /** @var string The type. */
    public $type;

    /**
     * Constructor.
     *
     * @param string $message The message.
     * @param string $type The type.
     */
    public function __construct($message, $type = self::INFO) {
        $this->message = $message;
        $this->type = $type;
    }

}
