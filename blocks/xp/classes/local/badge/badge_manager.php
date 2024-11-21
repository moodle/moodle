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
 * Badge manager.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\badge;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/lib/awardlib.php');

/**
 * Badge manager.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badge_manager {

    /** @var \moodle_database The DB. */
    protected $db;

    /**
     * Constructor.
     *
     * @param \moodle_database $db
     */
    public function __construct(\moodle_database $db) {
        $this->db = $db;
    }

    /**
     * Award badge (stub).
     *
     * @param int $userid The user ID.
     * @param int $badgeid The badge ID.
     * @param int $issuerid The issuer ID.
     */
    public function award_badge($userid, $badgeid, $issuerid) {
    }

    /**
     * Get compatible badges (stub).
     *
     * @param \context $context The context.
     * @param int $userid The user ID.
     * @return object[] Indexed by ID.
     */
    public function get_compatible_badges(\context $context, $userid) {
        return [];
    }

    /**
     * Whether the badge is a site badge.
     *
     * @param int $badgeid The badge ID.
     * @return bool
     */
    public function is_site_badge($badgeid) {
        return $this->db->record_exists('badge', ['id' => $badgeid, 'type' => BADGE_TYPE_SITE]);
    }
}
