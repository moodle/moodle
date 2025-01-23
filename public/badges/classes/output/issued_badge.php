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

use core_badges\local\backpack\helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');

use context_course;
use context_system;
use stdClass;
use renderable;
use core_badges\badge;
use moodle_url;
use renderer_base;

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
        // Get achievement credential data using OBv2.0 specification.
        // In the future, we may want to support other OB versions.
        $this->issued = helper::export_achievement_credential(OPEN_BADGES_V2, $hash);
        if (array_key_exists('issuedOn', $this->issued) && !is_numeric($this->issued['issuedOn'])) {
            $this->issued['issuedOn'] = strtotime($this->issued['issuedOn']);
        }

        $rec = $DB->get_record_sql('SELECT userid, visible, badgeid
                FROM {badge_issued}
                WHERE ' . $DB->sql_compare_text('uniquehash', 40) . ' = ' . $DB->sql_compare_text(':hash', 40),
                array('hash' => $hash), IGNORE_MISSING);
        if ($rec) {
            // Get a recipient from database.
            $userfieldsapi = \core_user\fields::for_name();
            $namefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
            $user = $DB->get_record_sql("SELECT u.id, $namefields, u.deleted, u.email
                        FROM {user} u WHERE u.id = :userid", array('userid' => $rec->userid));
            $this->recipient = $user;
            $this->visible = $rec->visible;
            $this->badgeid = $rec->badgeid;

            // Get the badge class using OBv2.0 specification.
            // In the future, we may want to support other OB versions.
            $this->badgeclass = helper::export_credential(
                OPEN_BADGES_V2,
                $this->badgeid,
            );
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $CFG, $DB, $SITE, $USER;

        $now = time();
        if (isset($this->issued['expires'])) {
            if (!is_numeric($this->issued['expires'])) {
                $this->issued['expires'] = strtotime($this->issued['expires']);
            }
            $expiration = $this->issued['expires'];
        } else {
            $expiration = $now + 86400;
        }

        $context = null;
        $data = new stdClass();
        $badge = new badge($this->badgeid);
        if ($badge->type == BADGE_TYPE_COURSE && isset($badge->courseid)) {
            $context = context_course::instance($badge->courseid);
            $data->coursefullname = format_string($DB->get_field('course', 'fullname', ['id' => $badge->courseid]),
                true, ['context' => $context]);
        } else {
            $context = context_system::instance();
            $data->sitefullname = format_string($SITE->fullname, true, ['context' => $context]);
        }

        // Field: Image.
        $data->badgeimage = is_array($this->badgeclass['image']) ? $this->badgeclass['image']['id'] : $this->badgeclass['image'];

        // Field: Expiration date.
        if (isset($this->issued['expires'])) {
            if ($expiration < $now) {
                $data->expireddate = $this->issued['expires'];
                $data->expireddateformatted = userdate($this->issued['expires'], get_string('strftimedatetime', 'langconfig'));
            } else {
                $data->expiredate = $this->issued['expires'];
            }
        }

        // Fields: Name, description, issuedOn.
        $data->badgename = $badge->name;
        $data->badgedescription = $badge->description;
        $data->badgeissuedon = $this->issued['issuedOn'];

        // Field: Recipient (the badge was awarded to this person).
        if ($this->recipient->deleted) {
            $strdata = new stdClass();
            $strdata->user = fullname($this->recipient);
            $strdata->site = format_string($SITE->fullname, true, ['context' => context_system::instance()]);
            $data->recipientname = get_string('error:userdeleted', 'badges', $strdata);
        } else {
            $data->recipientname = fullname($this->recipient);
        }

        // Field: Criteria.
        // This method will return the HTML with the badge criteria.
        $data->criteria = $output->print_badge_criteria($badge);

        // Field: Issuer.
        $data->issuedby = format_string($badge->issuername, true, ['context' => $context]);
        if (isset($badge->issuercontact) && !empty($badge->issuercontact)) {
            $data->issuedbyemailobfuscated = obfuscate_mailto($badge->issuercontact, $data->issuedby);
        }

        // Fields: Other details, such as language or version.
        $data->hasotherfields = false;
        if (!empty($badge->language)) {
            $data->hasotherfields = true;
            $languages = get_string_manager()->get_list_of_languages();
            $data->language = $languages[$badge->language];
        }
        if (!empty($badge->version)) {
            $data->hasotherfields = true;
            $data->version = $badge->version;
        }
        if (!empty($badge->imagecaption)) {
            $data->hasotherfields = true;
            $data->imagecaption = $badge->imagecaption;
        }

        // Field: Endorsement.
        $endorsement = $badge->get_endorsement();
        if (!empty($endorsement)) {
            $data->hasotherfields = true;
            $endorsement = $badge->get_endorsement();
            $endorsement->issueremail = obfuscate_mailto($endorsement->issueremail, $endorsement->issueremail);
            $data->endorsement = (array) $endorsement;
        }

        // Field: Related badges.
        $relatedbadges = $badge->get_related_badges(true);
        if (!empty($relatedbadges)) {
            $data->hasotherfields = true;
            $data->hasrelatedbadges = true;
            $data->relatedbadges = [];
            foreach ($relatedbadges as $related) {
                if (isloggedin() && ($context instanceof context_course && !is_guest($context))) {
                    $related->url = (new moodle_url('/badges/overview.php', ['id' => $related->id]))->out(false);
                }
                $data->relatedbadges[] = (array)$related;
            }
        }

        // Field: Alignments.
        $alignments = $badge->get_alignments();
        if (!empty($alignments)) {
            $data->hasotherfields = true;
            $data->hasalignments = true;
            $data->alignments = [];
            foreach ($alignments as $alignment) {
                $data->alignments[] = (array)$alignment;
            }
        }

        // Buttons to display.
        if ($USER->id == $this->recipient->id && !empty($CFG->enablebadges)) {
            $data->downloadurl = (new moodle_url('/badges/badge.php', ['hash' => $this->hash, 'bake' => true]))->out(false);

            if (!empty($CFG->badges_allowexternalbackpack) && ($expiration > $now)
                && $userbackpack = badges_get_user_backpack($USER->id)) {

                if (badges_open_badges_backpack_api($userbackpack->id) == OPEN_BADGES_V2P1) {
                    $addtobackpackurl = new moodle_url('/badges/backpack-export.php', ['hash' => $this->hash]);
                } else {
                    $addtobackpackurl = new moodle_url('/badges/backpack-add.php', ['hash' => $this->hash]);
                }
                $data->addtobackpackurl = $addtobackpackurl->out(false);
            }
        }

        // Field: Tags.
        $tags = \core_tag_tag::get_item_tags('core_badges', 'badge', $this->badgeid);
        $taglist = new \core_tag\output\taglist($tags);
        $data->badgetag = $taglist->export_for_template($output);

        return $data;
    }
}
