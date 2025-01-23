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

namespace core_badges;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/renderer.php');

/**
 * Class that represents badge assertion, also known as achievement credential from OBv3.0 onwards.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class achievement_credential {
    /** @var object Issued badge information from database */
    private $data;

    /**
     * Constructor is protected so that devs are forced to use self::instance().
     *
     * @param string $hash Badge unique hash from badge_issued table.
     */
    protected function __construct(
        string $hash,
    ) {
        global $DB;

        $this->data = $DB->get_record_sql(
            'SELECT
                bi.dateissued,
                bi.dateexpire,
                bi.uniquehash,
                u.email,
                u.id as userid,
                b.*,
                bb.email as backpackemail
            FROM
                {badge} b
                JOIN {badge_issued} bi
                    ON b.id = bi.badgeid
                JOIN {user} u
                    ON u.id = bi.userid
                LEFT JOIN {badge_backpack} bb
                    ON bb.userid = bi.userid
            WHERE ' . $DB->sql_compare_text('bi.uniquehash', 40) . ' = ' . $DB->sql_compare_text(':hash', 40),
            ['hash' => $hash],
            IGNORE_MISSING,
        );
    }

    /**
     * Create an instance of the achievement_credential class.
     *
     * @param string $hash
     * @return self|null The instance of the achievement_credential class or null if the hash does not exist.
     */
    public static function instance(string $hash): ?self {
        $achievement = new self($hash);
        if (!$achievement->data) {
            return null;
        }

        return $achievement;
    }

    /**
     * Get the local id for this badge.
     *
     * @return int Badge id.
     */
    public function get_badge_id(): int {
        if ($this->data) {
            return $this->data->id;
        }
        return 0;
    }

    /**
     * Get the badge unique hash for this achievement credential.
     *
     * @return string Badge unique hash.
     */
    public function get_hash(): string {
        $hash = '';
        if ($this->data) {
            $hash = $this->data->uniquehash;
        }
        return $hash;
    }

    /**
     * Get the backpack email if available, otherwise the user's email.
     *
     * @return string Email address of the user who received the badge.
     */
    public function get_email(): string {
        return $this->data->backpackemail ?: $this->data->email;
    }

    /**
     * Get the date this badge was issued.
     *
     * @return int Date issued in milliseconds since the epoch.
     */
    public function get_dateissued(): int {
        return $this->data->dateissued;
    }

    /**
     * Get the date this badge expires.
     *
     * @return int|null Date expire in milliseconds since the epoch, or null if it does not expire.
     */
    public function get_dateexpire(): ?int {
        return $this->data->dateexpire;
    }

    /**
     * Get the tags associated with this badge.
     *
     * @return array An array of tags associated with the badge.
     */
    public function get_tags(): array {
        return array_values(\core_tag_tag::get_item_tags_array('core_badges', 'badge', $this->get_badge_id()));
    }
}
