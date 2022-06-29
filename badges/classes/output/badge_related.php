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
 * Collection of all related badges.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

namespace core_badges\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');

use renderable;

/**
 * Collection of all related badges.
 *
 * @copyright  2018 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
class badge_related implements renderable {

    /** @var string how are the data sorted. */
    public $sort = 'name';

    /** @var string how are the data sorted. */
    public $dir = 'ASC';

    /** @var int page number to display. */
    public $page = 0;

    /** @var int number of badges to display per page. */
    public $perpage = BADGE_PERPAGE;

    /** @var int the total number of badges to display. */
    public $totalcount = null;

    /** @var int the current badge. */
    public $currentbadgeid = 0;

    /** @var array list of badges. */
    public $badges = array();

    /**
     * Initializes the list of badges to display.
     *
     * @param array $badges related badges to render.
     * @param int $currentbadgeid ID current badge.
     */
    public function __construct($badges, $currentbadgeid) {
        $this->badges = $badges;
        $this->currentbadgeid = $currentbadgeid;
    }
}

