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
 * XP activity.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\activity;

use DateTime;

/**
 * XP activity.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xp_activity implements activity, activity_with_xp {

    /** @var DateTime The date. */
    protected $date;
    /** @var lang_string The description. */
    protected $desc;
    /** @var int The XP. */
    protected $xp;

    /**
     * Constructor.
     *
     * @param DateTime $date The date.
     * @param string|lang_string $desc The description.
     * @param int $xp The XP.
     */
    public function __construct(DateTime $date, $desc, $xp) {
        $this->date = $date;
        $this->desc = $desc;
        $this->xp = $xp;
    }

    /**
     * Date.
     *
     * @return DateTime
     */
    public function get_date() {
        return $this->date;
    }

    /**
     * Description.
     *
     * @return The description.
     */
    public function get_description() {
        return (string) $this->desc;
    }

    /**
     * The XP earned at this stage.
     *
     * @return int
     */
    public function get_xp() {
        return $this->xp;
    }

}
