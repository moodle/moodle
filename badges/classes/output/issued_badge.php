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
 * Issued badge renderable.
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
 * An issued badges for badge.php page
 *
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class issued_badge implements renderable {
    /** @var issued badge */
    public $issued;

    /** @var badge recipient */
    public $recipient;

    /** @var badge class */
    public $badgeclass;

    /** @var badge visibility to others */
    public $visible = 0;

    /** @var badge class */
    public $badgeid = 0;

    /** @var unique hash identifying the issued badge */
    public $hash;

    /**
     * Initializes the badge to display
     *
     * @param string $hash Issued badge hash
     */
    public function __construct($hash) {
        global $DB;

        $this->hash = $hash;
        $assertion = new \core_badges_assertion($hash, badges_open_badges_backpack_api());
        $this->issued = $assertion->get_badge_assertion();
        $this->badgeclass = $assertion->get_badge_class();

        $rec = $DB->get_record_sql('SELECT userid, visible, badgeid
                FROM {badge_issued}
                WHERE ' . $DB->sql_compare_text('uniquehash', 40) . ' = ' . $DB->sql_compare_text(':hash', 40),
                array('hash' => $hash), IGNORE_MISSING);
        if ($rec) {
            // Get a recipient from database.
            $namefields = get_all_user_name_fields(true, 'u');
            $user = $DB->get_record_sql("SELECT u.id, $namefields, u.deleted, u.email
                        FROM {user} u WHERE u.id = :userid", array('userid' => $rec->userid));
            $this->recipient = $user;
            $this->visible = $rec->visible;
            $this->badgeid = $rec->badgeid;
        }
    }
}

