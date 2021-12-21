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
 * Contains user badge class for displaying a badge issued to a user.
 *
 * @package   core_badges
 * @copyright 2018 Dani Palou <dani@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use moodle_url;
use core_badges\external\endorsement_exporter;
use core_badges\external\alignment_exporter;
use core_badges\external\related_info_exporter;

/**
 * Class for displaying a badge issued to a user.
 *
 * @package   core_badges
 * @copyright 2018 Dani Palou <dani@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_badge_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Badge id',
                'optional' => true,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Badge name',
            ],
            'description' => [
                'type' => PARAM_NOTAGS,
                'description' => 'Badge description',
                'null' => NULL_ALLOWED,
            ],
            'timecreated' => [
                'type' => PARAM_INT,
                'description' => 'Time created',
                'optional' => true,
                'default' => 0,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Time modified',
                'optional' => true,
                'default' => 0,
            ],
            'usercreated' => [
                'type' => PARAM_INT,
                'description' => 'User created',
                'optional' => true,
            ],
            'usermodified' => [
                'type' => PARAM_INT,
                'description' => 'User modified',
                'optional' => true,
            ],
            'issuername' => [
                'type' => PARAM_NOTAGS,
                'description' => 'Issuer name',
            ],
            'issuerurl' => [
                'type' => PARAM_URL,
                'description' => 'Issuer URL',
            ],
            'issuercontact' => [
                'type' => PARAM_RAW,
                'description' => 'Issuer contact',
                'null' => NULL_ALLOWED,
            ],
            'expiredate' => [
                'type' => PARAM_INT,
                'description' => 'Expire date',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'expireperiod' => [
                'type' => PARAM_INT,
                'description' => 'Expire period',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'type' => [
                'type' => PARAM_INT,
                'description' => 'Type',
                'optional' => true,
                'default' => 1,
            ],
            'courseid' => [
                'type' => PARAM_INT,
                'description' => 'Course id',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'message' => [
                'type' => PARAM_RAW,
                'description' => 'Message',
                'optional' => true,
            ],
            'messagesubject' => [
                'type' => PARAM_TEXT,
                'description' => 'Message subject',
                'optional' => true,
            ],
            'attachment' => [
                'type' => PARAM_INT,
                'description' => 'Attachment',
                'optional' => true,
                'default' => 1,
            ],
            'notification' => [
                'type' => PARAM_INT,
                'description' => 'Whether to notify when badge is awarded',
                'optional' => true,
                'default' => 1,
            ],
            'nextcron' => [
                'type' => PARAM_INT,
                'description' => 'Next cron',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'status' => [
                'type' => PARAM_INT,
                'description' => 'Status',
                'optional' => true,
                'default' => 0,
            ],
            'issuedid' => [
                'type' => PARAM_INT,
                'description' => 'Issued id',
                'optional' => true,
            ],
            'uniquehash' => [
                'type' => PARAM_ALPHANUM,
                'description' => 'Unique hash',
            ],
            'dateissued' => [
                'type' => PARAM_INT,
                'description' => 'Date issued',
                'default' => 0,
            ],
            'dateexpire' => [
                'type' => PARAM_INT,
                'description' => 'Date expire',
                'null' => NULL_ALLOWED,
            ],
            'visible' => [
                'type' => PARAM_INT,
                'description' => 'Visible',
                'optional' => true,
                'default' => 0,
            ],
            'email' => [
                'type' => PARAM_TEXT,
                'description' => 'User email',
                'optional' => true,
            ],
            'version' => [
                'type' => PARAM_TEXT,
                'description' => 'Version',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'language' => [
                'type' => PARAM_NOTAGS,
                'description' => 'Language',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'imageauthorname' => [
                'type' => PARAM_TEXT,
                'description' => 'Name of the image author',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'imageauthoremail' => [
                'type' => PARAM_TEXT,
                'description' => 'Email of the image author',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'imageauthorurl' => [
                'type' => PARAM_URL,
                'description' => 'URL of the image author',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
            'imagecaption' => [
                'type' => PARAM_TEXT,
                'description' => 'Caption of the image',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ],
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return array(
            'context' => 'context',
            'endorsement' => 'stdClass?',
            'alignment' => 'stdClass[]',
            'relatedbadges' => 'stdClass[]',
        );
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'badgeurl' => [
                'type' => PARAM_URL,
                'description' => 'Badge URL',
            ],
            'endorsement' => [
                'type' => endorsement_exporter::read_properties_definition(),
                'description' => 'Badge endorsement',
                'optional' => true,
            ],
            'alignment' => [
                'type' => alignment_exporter::read_properties_definition(),
                'description' => 'Badge alignments',
                'multiple' => true,
            ],
            'relatedbadges' => [
                'type' => related_info_exporter::read_properties_definition(),
                'description' => 'Related badges',
                'multiple' => true,
            ]
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $context = $this->related['context'];
        $endorsement = $this->related['endorsement'];
        $alignments = $this->related['alignment'];
        $relatedbadges = $this->related['relatedbadges'];

        $values = array(
            'badgeurl' => moodle_url::make_webservice_pluginfile_url($context->id, 'badges', 'badgeimage', $this->data->id, '/',
                'f3')->out(false),
            'alignment' => array(),
            'relatedbadges' => array(),
        );

        if ($endorsement) {
            $endorsementexporter = new endorsement_exporter($endorsement, array('context' => $context));
            $values['endorsement'] = $endorsementexporter->export($output);
        }

        if (!empty($alignments)) {
            foreach ($alignments as $alignment) {
                $alignmentexporter = new alignment_exporter($alignment, array('context' => $context));
                $values['alignment'][] = $alignmentexporter->export($output);
            }
        }

        if (!empty($relatedbadges)) {
            foreach ($relatedbadges as $badge) {
                $relatedexporter = new related_info_exporter($badge, array('context' => $context));
                $values['relatedbadges'][] = $relatedexporter->export($output);
            }
        }

        return $values;
    }
}
