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
 * Badge assertion library.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Open Badges Assertions specification 1.0 {@link https://github.com/mozilla/openbadges/wiki/Assertions}
 *
 * Badge asserion is defined by three parts:
 * - Badge Assertion (information regarding a specific badge that was awarded to a badge earner)
 * - Badge Class (general information about a badge and what it is intended to represent)
 * - Issuer Class (general information of an issuing organisation)
 */
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/renderer.php');

/**
 * Class that represents badge assertion.
 *
 */
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
     */
    public function __construct($hash, $obversion = OPEN_BADGES_V2) {
        global $DB;

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
     */
    public function get_badge_id() {
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
     */
    public function get_assertion_hash() {
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
     */
    public function get_badge_assertion($issued = true, $usesalt = true) {
        global $CFG;
        $assertion = array();
        if ($this->_data) {
            $hash = $this->_data->uniquehash;
            $email = empty($this->_data->backpackemail) ? $this->_data->email : $this->_data->backpackemail;
            $assertionurl = new moodle_url('/badges/assertion.php', array('b' => $hash, 'obversion' => $this->_obversion));

            if ($this->_obversion == OPEN_BADGES_V2) {
                $classurl = new moodle_url('/badges/badge_json.php', array('id' => $this->get_badge_id()));
            } else {
                $classurl = new moodle_url('/badges/assertion.php', array('b' => $hash, 'action' => 1));
            }

            // Required.
            $assertion['uid'] = $hash;
            $assertion['recipient'] = array();
            if ($usesalt) {
                $assertion['recipient']['identity'] = 'sha256$' . hash('sha256', $email . $CFG->badges_badgesalt);
            } else {
                $assertion['recipient']['identity'] = $email;
            }
            $assertion['recipient']['type'] = 'email'; // Currently the only supported type.
            $assertion['recipient']['hashed'] = true; // We are always hashing recipient.
            if ($usesalt) {
                $assertion['recipient']['salt'] = $CFG->badges_badgesalt;
            }
            if ($issued) {
                $assertion['badge'] = $classurl->out(false);
            }
            $assertion['verify'] = array();
            $assertion['verify']['type'] = 'hosted'; // 'Signed' is not implemented yet.
            $assertion['verify']['url'] = $assertionurl->out(false);
            $assertion['issuedOn'] = $this->_data->dateissued;
            if ($issued) {
                $assertion['evidence'] = $this->_url->out(false); // Currently issued badge URL.
            }
            // Optional.
            if (!empty($this->_data->dateexpire)) {
                $assertion['expires'] = $this->_data->dateexpire;
            }
            $this->embed_data_badge_version2($assertion, OPEN_BADGES_V2_TYPE_ASSERTION);
        }
        return $assertion;
    }

    /**
     * Get badge class information.
     *
     * @param boolean $issued Include the nested badge issuer information.
     * @return array Badge Class information.
     */
    public function get_badge_class($issued = true) {
        $class = [];
        if ($this->_data) {
            if (empty($this->_data->courseid)) {
                $context = context_system::instance();
            } else {
                $context = context_course::instance($this->_data->courseid);
            }
            // Required.
            $class['name'] = $this->_data->name;
            $class['description'] = $this->_data->description;
            $storage = get_file_storage();
            $imagefile = $storage->get_file($context->id, 'badges', 'badgeimage', $this->_data->id, '/', 'f3.png');
            if ($imagefile) {
                $imagedata = base64_encode($imagefile->get_content());
            } else {
                if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
                    // Unit tests the file might not exist yet.
                    $imagedata = '';
                } else {
                    throw new coding_exception('Image file does not exist.');
                }
            }
            $class['image'] = 'data:image/png;base64,' . $imagedata;
            $class['criteria'] = $this->_url->out(false); // Currently issued badge URL.
            if ($issued) {
                $params = ['id' => $this->get_badge_id(), 'obversion' => $this->_obversion];
                $issuerurl = new moodle_url('/badges/issuer_json.php', $params);
                $class['issuer'] = $issuerurl->out(false);
            }
            $this->embed_data_badge_version2($class, OPEN_BADGES_V2_TYPE_BADGE);
            if (!$issued) {
                unset($class['issuer']);
            }
        }
        return $class;
    }

    /**
     * Get badge issuer information.
     *
     * @return array Issuer information.
     */
    public function get_issuer() {
        global $CFG;
        $issuer = array();
        if ($this->_data) {
            // Required.
            if ($this->_obversion == OPEN_BADGES_V1) {
                $issuer['name'] = $this->_data->issuername;
                $issuer['url'] = $this->_data->issuerurl;
                // Optional.
                if (!empty($this->_data->issuercontact)) {
                    $issuer['email'] = $this->_data->issuercontact;
                } else {
                    $issuer['email'] = $CFG->badges_defaultissuercontact;
                }
            } else {
                $badge = new badge($this->get_badge_id());
                $issuer = $badge->get_badge_issuer();
            }
        }
        $this->embed_data_badge_version2($issuer, OPEN_BADGES_V2_TYPE_ISSUER);
        return $issuer;
    }

    /**
     * Get related badges of the badge.
     *
     * @param badge $badge Badge object.
     * @return array|bool List related badges.
     */
    public function get_related_badges(badge $badge) {
        global $DB;
        $arraybadges = array();
        $relatedbadges = $badge->get_related_badges(true);
        if ($relatedbadges) {
            foreach ($relatedbadges as $rb) {
                $url = new moodle_url('/badges/badge_json.php', array('id' => $rb->id));
                $arraybadges[] = array(
                    'id'        => $url->out(false),
                    'version'   => $rb->version,
                    '@language' => $rb->language
                );
            }
        }
        return $arraybadges;
    }

    /**
     * Get endorsement of the badge.
     *
     * @return false|stdClass Endorsement information.
     */
    public function get_endorsement() {
        global $DB;
        $endorsement = array();
        $record = $DB->get_record_select('badge_endorsement', 'badgeid = ?', array($this->_data->id));
        return $record;
    }

    /**
     * Get criteria of badge class.
     *
     * @return array|string Criteria information.
     */
    public function get_criteria_badge_class() {
        $badge = new badge($this->_data->id);
        $narrative = $badge->markdown_badge_criteria();
        if (!empty($narrative)) {
            $criteria = array();
            $criteria['id'] = $this->_url->out(false);
            $criteria['narrative'] = $narrative;
            return $criteria;
        } else {
            return $this->_url->out(false);
        }
    }

    /**
     * Get alignment of the badge.
     *
     * @return array information.
     */
    public function get_alignments() {
        global $DB;
        $badgeid = $this->_data->id;
        $alignments = array();
        $items = $DB->get_records_select('badge_alignment', 'badgeid = ?', array($badgeid));
        foreach ($items as $item) {
            $alignment = array('targetName' => $item->targetname, 'targetUrl' => $item->targeturl);
            if ($item->targetdescription) {
                $alignment['targetDescription'] = $item->targetdescription;
            }
            if ($item->targetframework) {
                $alignment['targetFramework'] = $item->targetframework;
            }
            if ($item->targetcode) {
                $alignment['targetCode'] = $item->targetcode;
            }
            $alignments[] = $alignment;
        }
        return $alignments;
    }

    /**
     * Embed data of Open Badges Specification Version 2.0 to json.
     *
     * @param array $json for assertion, badges, issuer.
     * @param string $type Content type.
     */
    protected function embed_data_badge_version2 (&$json, $type = OPEN_BADGES_V2_TYPE_ASSERTION) {
        // Specification Version 2.0.
        if ($this->_obversion == OPEN_BADGES_V2) {
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
                if ($endorsement = $this->get_endorsement()) {
                    $endorsementurl = new moodle_url('/badges/endorsement_json.php', array('id' => $this->_data->id));
                    $json['endorsement'] = $endorsementurl->out(false);
                }
                if ($alignments = $this->get_alignments()) {
                    $json['alignments'] = $alignments;
                }
                if ($this->_data->imageauthorname ||
                        $this->_data->imageauthoremail ||
                        $this->_data->imageauthorurl ||
                        $this->_data->imagecaption) {
                    $storage = get_file_storage();
                    $imagefile = $storage->get_file($context->id, 'badges', 'badgeimage', $this->_data->id, '/', 'f1.png');
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
}
