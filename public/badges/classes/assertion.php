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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/renderer.php');

use core_badges\local\backpack\helper;
use core_badges\local\backpack\ob_factory;

/**
 * Open Badges Assertions specification 2.0
 * {@link https://www.imsglobal.org/sites/default/files/Badges/OBv2p0Final/index.html#Assertion}
 *
 * Badge assertion is defined by three parts:
 * - Badge Assertion (information regarding a specific badge that was awarded to a badge earner)
 * - Badge Class (general information about a badge and what it is intended to represent)
 * - Issuer Class (general information of an issuing organisation)
 *
 * @package    core_badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 * @deprecated since Moodle 5.1.
 * @todo       MDL-85730 Final deprecation in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: '\core_badges\achievement_credential',
    since: '5.1',
    mdl: 'MDL-85621',
)]
class core_badges_assertion {
    /** @var object Issued badge information from database */
    private $_data;

    /** @var moodle_url Issued badge url */
    private $_url;

    /** @var int $obversion to control version JSON-LD. */
    private $_obversion = OPEN_BADGES_V2;

    /**
     * Constructs with issued badge unique hash.
     *
     * @param string $hash Badge unique hash from badge_issued table.
     * @param int $obversion to control version JSON-LD.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\achievement_credential::instance()',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function __construct($hash, $obversion = OPEN_BADGES_V2) {
        global $DB;

        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        $this->_data = $DB->get_record_sql('
            SELECT
                bi.dateissued,
                bi.dateexpire,
                bi.uniquehash,
                u.email,
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
            array('hash' => $hash), IGNORE_MISSING);

        if ($this->_data) {
            $this->_url = new moodle_url('/badges/badge.php', array('hash' => $this->_data->uniquehash));
        } else {
            $this->_url = new moodle_url('/badges/badge.php');
        }
        $this->_obversion = $obversion;
    }

    /**
     * Get the local id for this badge.
     *
     * @return int
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\achievement_credential::get_badge_id()',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_badge_id() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        $badgeid = 0;
        if ($this->_data) {
            $badgeid = $this->_data->id;
        }
        return $badgeid;
    }

    /**
     * Get the local id for this badge assertion.
     *
     * @return string
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\achievement_credential::get_hash()',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_assertion_hash() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        $hash = '';
        if ($this->_data) {
            $hash = $this->_data->uniquehash;
        }
        return $hash;
    }

    /**
     * Get badge assertion.
     *
     * @param boolean $issued Include the nested badge issued information.
     * @param boolean $usesalt Hash the identity and include the salt information for the hash.
     * @return array Badge assertion.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\local\backpack\helper::export_achievement_credential',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_badge_assertion($issued = true, $usesalt = true) {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        return helper::export_achievement_credential(
            helper::convert_apiversion($this->_obversion),
            $this->get_assertion_hash(),
            $issued,
            $usesalt,
        );
    }

    /**
     * Get badge class information.
     *
     * @param boolean $issued Include the nested badge issuer information.
     * @return array Badge Class information.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\local\backpack\helper::export_credential',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_badge_class($issued = true) {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        return helper::export_credential(
            helper::convert_apiversion($this->_obversion),
            $this->get_badge_id(),
            $issued,
        );
    }

    /**
     * Get badge issuer information.
     *
     * @return array Issuer information.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\local\backpack\helper::export_issuer',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_issuer() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        return helper::export_issuer(
            helper::convert_apiversion($this->_obversion),
            $this->get_badge_id(),
        );
    }

    /**
     * Get related badges of the badge.
     *
     * @param badge $badge Badge object.
     * @return array|bool List related badges.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\local\backpack\ob\badge_exporter_interface::export_related_badges',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_related_badges(badge $badge) {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        $badgeexporter = ob_factory::create_badge_exporter_from_id(
            $badge->id,
            helper::convert_apiversion($this->_obversion),
        );
        return $badgeexporter->export_related_badges();
    }

    /**
     * Get endorsement of the badge.
     *
     * @return false|stdClass Endorsement information.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_endorsement() {
        global $DB;

        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        $record = $DB->get_record_select('badge_endorsement', 'badgeid = ?', array($this->_data->id));
        return $record;
    }

    /**
     * Get criteria of badge class.
     *
     * @return array|string Criteria information.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\local\backpack\ob\badge_exporter_interface::export_criteria',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_criteria_badge_class() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        $badgeexporter = ob_factory::create_badge_exporter_from_id(
            $this->get_badge_id(),
            helper::convert_apiversion(OPEN_BADGES_V2),
        );
        return $badgeexporter->export_criteria();
    }

    /**
     * Get alignment of the badge.
     *
     * @return array information.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: '\core_badges\local\backpack\ob\badge_exporter_interface::export_alignments',
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_alignments() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        $badgeexporter = ob_factory::create_badge_exporter_from_id(
            $this->get_badge_id(),
            helper::convert_apiversion(OPEN_BADGES_V2),
        );
        return $badgeexporter->export_alignments();
    }

    /**
     * Embed data of Open Badges Specification Version 2.0 to json.
     *
     * @param array $json for assertion, badges, issuer.
     * @param string $type Content type.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    protected function embed_data_badge_version2(&$json, $type = OPEN_BADGES_V2_TYPE_ASSERTION) {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        // Specification Version 2.0.
        if ($this->_obversion >= OPEN_BADGES_V2) {
            $badge = new badge($this->_data->id);
            if (empty($this->_data->courseid)) {
                $context = context_system::instance();
            } else {
                $context = context_course::instance($this->_data->courseid);
            }

            $hash = $this->_data->uniquehash;
            $assertionsurl = new moodle_url('/badges/assertion.php', array('b' => $hash, 'obversion' => $this->_obversion));
            $classurl = new moodle_url(
                '/badges/badge_json.php',
                array('id' => $this->get_badge_id())
            );
            $issuerurl = new moodle_url('/badges/issuer_json.php', ['id' => $this->get_badge_id()]);
            // For assertion.
            if ($type == OPEN_BADGES_V2_TYPE_ASSERTION) {
                $json['@context'] = OPEN_BADGES_V2_CONTEXT;
                $json['type'] = OPEN_BADGES_V2_TYPE_ASSERTION;
                $json['id'] = $assertionsurl->out(false);
                $json['badge'] = $this->get_badge_class();
                $json['issuedOn'] = date('c', $this->_data->dateissued);
                if (!empty($this->_data->dateexpire)) {
                    $json['expires'] = date('c', $this->_data->dateexpire);
                }
                unset($json['uid']);
            }
            // For Badge.
            if ($type == OPEN_BADGES_V2_TYPE_BADGE) {
                $json['@context'] = OPEN_BADGES_V2_CONTEXT;
                $json['id'] = $classurl->out(false);
                $json['type'] = OPEN_BADGES_V2_TYPE_BADGE;
                $json['version'] = $this->_data->version;
                $json['criteria'] = $this->get_criteria_badge_class();
                $json['issuer'] = $this->get_issuer();
                $json['@language'] = $this->_data->language;
                if (!empty($relatedbadges = $this->get_related_badges($badge))) {
                    $json['related'] = $relatedbadges;
                }
                if ($alignments = $this->get_alignments()) {
                    $json['alignments'] = $alignments;
                }
                if ($this->_data->imagecaption) {
                    $storage = get_file_storage();
                    $imagefile = $storage->get_file($context->id, 'badges', 'badgeimage', $this->_data->id, '/', 'f3.png');
                    if ($imagefile) {
                        $imagedata = base64_encode($imagefile->get_content());
                    } else {
                        // The file might not exist in unit tests.
                        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
                            $imagedata = '';
                        } else {
                            throw new coding_exception('Image file does not exist.');
                        }
                    }
                    $json['image'] = 'data:image/png;base64,' . $imagedata;
                }
            }

            // For issuer.
            if ($type == OPEN_BADGES_V2_TYPE_ISSUER) {
                $json['@context'] = OPEN_BADGES_V2_CONTEXT;
                $json['id'] = $issuerurl->out(false);
                $json['type'] = OPEN_BADGES_V2_TYPE_ISSUER;
            }
        }
    }

    /**
     * Get tags of the badge.
     *
     * @return array tags.
     * @deprecated since Moodle 5.1.
     * @todo       MDL-85730 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        since: '5.1',
        mdl: 'MDL-85621',
    )]
    public function get_tags(): array {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);

        return array_values(\core_tag_tag::get_item_tags_array('core_badges', 'badge', $this->get_badge_id()));
    }
}
