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
 * Serve BadgeClass JSON for related badge.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2018 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', true); // No need for a session here.

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$id = required_param('id', PARAM_INT);
$action = optional_param('action', null, PARAM_INT); // Generates badge class if true.
$json = array();
$badge = new badge($id);
if ($badge->status != BADGE_STATUS_INACTIVE) {
    if (is_null($action)) {
        // Get the content of badge class.
        if (empty($badge->courseid)) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($badge->courseid);
        }
        $urlimage = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', 'f3')->out(false);

        $url = new moodle_url('/badges/badge_json.php', array('id' => $badge->id));

        $json['name'] = $badge->name;
        $json['description'] = $badge->description;
        $urlimage = moodle_url::make_pluginfile_url($context->id,
            'badges', 'badgeimage', $badge->id, '/', 'f3')->out(false);
        $json['image'] = [];
        $json['image']['id'] = $urlimage;
        if ($badge->imagecaption) {
            $json['image']['caption'] = $badge->imagecaption;
        }

        $params = ['id' => $badge->id];
        $badgeurl = new moodle_url('/badges/badgeclass.php', $params);
        $json['criteria']['id'] = $badgeurl->out(false);
        $json['criteria']['narrative'] = $badge->markdown_badge_criteria();
        $json['issuer'] = $badge->get_badge_issuer();
        $json['@context'] = OPEN_BADGES_V2_CONTEXT;
        $json['id'] = $url->out();
        $json['type'] = OPEN_BADGES_V2_TYPE_BADGE;
        if (!empty($badge->version)) {
            $json['version'] = $badge->version;
        }
        if (!empty($badge->language)) {
            $json['@language'] = $badge->language;
        }
        $badgetags = $badge->get_badge_tags();
        if ($badgetags) {
            $json['tags'] = $badgetags;
        }

        $relatedbadges = $badge->get_related_badges(true);
        if (!empty($relatedbadges)) {
            foreach ($relatedbadges as $related) {
                $relatedurl = new moodle_url('/badges/badge_json.php', array('id' => $related->id));
                $relateds[] = array('id' => $relatedurl->out(false),
                    'version' => $related->version, '@language' => $related->language);
            }
             $json['related'] = $relateds;
        }

        $alignments = $badge->get_alignments();
        if (!empty($alignments)) {
            foreach ($alignments as $item) {
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
                $json['alignments'][] = $alignment;
            }
        }
    } else if ($action == 0) {
        // Get the content for issuer.
        $json = $badge->get_badge_issuer();
    }
} else {
    // The badge doen't exist or not accessible for the users.
    header("HTTP/1.0 410 Gone");
    $badgeurl = new moodle_url('/badges/badge_json.php', array('id' => $id));
    $json['id'] = $badgeurl->out();
    $json['error'] = get_string('error:relatedbadgedoesntexist', 'badges');
}
echo $OUTPUT->header();
echo json_encode($json);
