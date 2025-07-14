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
 * External badge renderable.
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
use renderer_base;
use stdClass;

/**
 * An external badges for external.php page
 *
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_badge implements renderable {
    /** @var stdClass Issued badge */
    public $issued;

    /** @var int User ID */
    public $recipient;

    /** @var bool Validation of external badge */
    public $valid = true;

    /**
     * Initializes the badge to display
     *
     * @param stdClass $badge External badge information.
     * @param int $recipient User id.
     */
    public function __construct($badge, $recipient) {
        global $DB;
        // At this point a user has connected a backpack. So, we are going to get
        // their backpack email rather than their account email.
        $userfieldsapi = \core_user\fields::for_name();
        $namefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $user = $DB->get_record_sql("SELECT {$namefields}, b.email
                    FROM {user} u INNER JOIN {badge_backpack} b ON u.id = b.userid
                    WHERE b.userid = :userid", array('userid' => $recipient), IGNORE_MISSING);

        $this->issued = $badge;
        $this->recipient = $user;

        // Check if recipient is valid.
        // There is no way to be 100% sure that a badge belongs to a user.
        // Backpack does not return any recipient information.
        // All we can do is compare that backpack email hashed using salt
        // provided in the assertion matches a badge recipient from the assertion.
        if ($user) {
            if (isset($badge->assertion->recipient->identity)) {
                $badge->assertion->salt = $badge->assertion->recipient->salt;
                $badge->assertion->recipient = $badge->assertion->recipient->identity;
            }
            // Open Badges V2 does not even include a recipient.
            if (!isset($badge->assertion->recipient)) {
                $this->valid = false;
            } else if (validate_email($badge->assertion->recipient) && $badge->assertion->recipient == $user->email) {
                // If we have email, compare emails.
                $this->valid = true;
            } else if ($badge->assertion->recipient == 'sha256$' . hash('sha256', $user->email)) {
                // If recipient is hashed, but no salt, compare hashes without salt.
                $this->valid = true;
            } else if ($badge->assertion->recipient == 'sha256$' . hash('sha256', $user->email . $badge->assertion->salt)) {
                // If recipient is hashed, compare hashes.
                $this->valid = true;
            } else {
                // Otherwise, we cannot be sure that this user is a recipient.
                $this->valid = false;
            }
        } else {
            $this->valid = false;
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();

        $now = time();
        if (isset($this->issued->assertion->expires)) {
            if (!is_numeric($this->issued->assertion->expires)) {
                $this->issued->assertion->expires = strtotime($this->issued->assertion->expires);
            }
            $expiration = $this->issued->assertion->expires;
        } else {
            $expiration = $now + 86400;
        }

        // Field: Image.
        if (isset($this->issued->imageUrl)) {
            $this->issued->image = $this->issued->imageUrl;
        }
        $data->badgeimage = $this->issued->image;
        if (is_object($data->badgeimage)) {
            if (!empty($data->badgeimage->caption)) {
                $data->hasotherfields = true;
                $data->imagecaption = $data->badgeimage->caption;
            }
            $data->badgeimage = $data->badgeimage->id;
        }

        // Field: Expiration date.
        if (isset($this->issued->assertion->expires)) {
            if ($expiration < $now) {
                $data->expireddate = $this->issued->assertion->expires;
                $data->expireddateformatted = userdate(
                    $this->issued->assertion->expires,
                    get_string('strftimedatetime', 'langconfig')
                );
            } else {
                $data->expiredate = $this->issued->assertion->expires;
            }
        }

        // Fields: Name, description, issuedOn.
        $data->badgename = $this->issued->assertion->badge->name;
        $data->badgedescription = $this->issued->assertion->badge->description;
        if (isset($this->issued->assertion->issued_on)) {
            if (!is_numeric($this->issued->assertion->issued_on)) {
                $this->issued->assertion->issued_on = strtotime($this->issued->assertion->issued_on);
            }
            $data->badgeissuedon = $this->issued->assertion->issued_on;
        }

        // Field: Recipient (the badge was awarded to this person).
        $data->recipientname = fullname($this->recipient);
        if (!$this->valid) {
            $data->recipientnotification = new stdClass();
            $data->recipientnotification->message = get_string('recipientvalidationproblem', 'badges');
        }

        // Field: Criteria.
        if (isset($this->issued->assertion->badgeclass->criteria->narrative)) {
            $data->criteria = $this->issued->assertion->badgeclass->criteria->narrative;
        }

        // Field: Issuer.
        $data->issuedby = $this->issued->issuer->name;
        if (isset($this->issued->issuer->contact) && !empty($this->issued->issuer->contact)) {
            $data->issuedbyemailobfuscated = obfuscate_mailto($this->issued->issuer->contact, $data->issuedby);
        }

        // Field: Hosted URL.
        if (isset($this->issued->hostedUrl) && !empty($this->issued->hostedUrl)) {
            $data->hostedurl = $this->issued->hostedUrl;
        }

        return $data;
    }
}
