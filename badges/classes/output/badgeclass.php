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

namespace core_badges\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');

use coding_exception;
use context_course;
use stdClass;
use renderable;
use core_badges\badge;
use moodle_url;
use renderer_base;

/**
 * Page to display badge information, such as name, description or criteria. This information is unrelated to assertions.
 *
 * @package    core_badges
 * @copyright  2022 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badgeclass implements renderable {

    /** @var badge class */
    public $badge;

    /** @var badge class */
    public $badgeid = 0;

    /** @var \context The badge context*/
    public $context;

    /**
     * Initializes the badge to display.
     *
     * @param int $id Id of the badge to display.
     */
    public function __construct(int $id) {
        $this->badgeid = $id;
        $this->badge = new badge($this->badgeid);
        if ($this->badge->status == BADGE_STATUS_INACTIVE) {
            // Inactive badges that haven't been published previously can't be displayed.
            $this->badge = null;
        } else {
            $this->context = $this->badge->get_context();
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $DB, $SITE;

        $data = new stdClass();
        if ($this->context instanceof context_course) {
            $data->coursefullname = format_string($DB->get_field('course', 'fullname', ['id' => $this->badge->courseid]),
                true, ['context' => $this->context]);
        } else {
            $data->sitefullname = format_string($SITE->fullname, true, ['context' => $this->context]);
        }

        // Field: Image.
        $storage = get_file_storage();
        $imagefile = $storage->get_file($this->context->id, 'badges', 'badgeimage', $this->badgeid, '/', 'f3.png');
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
        $data->badgeimage = 'data:image/png;base64,' . $imagedata;

        // Fields: Name, description.
        $data->badgename = $this->badge->name;
        $data->badgedescription = $this->badge->description;

        // Field: Criteria.
        // This method will return the HTML with the badge criteria.
        $data->criteria = $output->print_badge_criteria($this->badge);

        // Field: Issuer.
        $data->issuedby = format_string($this->badge->issuername, true, ['context' => $this->context]);
        if (isset($this->badge->issuercontact) && !empty($this->badge->issuercontact)) {
            $data->issuedbyemailobfuscated = obfuscate_mailto($this->badge->issuercontact, $data->issuedby);
        }

        // Fields: Other details, such as language or version.
        $data->hasotherfields = false;
        if (!empty($this->badge->language)) {
            $data->hasotherfields = true;
            $languages = get_string_manager()->get_list_of_languages();
            $data->language = $languages[$this->badge->language];
        }
        if (!empty($this->badge->version)) {
            $data->hasotherfields = true;
            $data->version = $this->badge->version;
        }
        if (!empty($this->badge->imageauthorname)) {
            $data->hasotherfields = true;
            $data->imageauthorname = $this->badge->imageauthorname;
        }
        if (!empty($this->badge->imageauthoremail)) {
            $data->hasotherfields = true;
            $data->imageauthoremail = obfuscate_mailto($this->badge->imageauthoremail, $this->badge->imageauthoremail);
        }
        if (!empty($this->badge->imageauthorurl)) {
            $data->hasotherfields = true;
            $data->imageauthorurl = $this->badge->imageauthorurl;
        }
        if (!empty($this->badge->imagecaption)) {
            $data->hasotherfields = true;
            $data->imagecaption = $this->badge->imagecaption;
        }

        // Field: Endorsement.
        $endorsement = $this->badge->get_endorsement();
        if (!empty($endorsement)) {
            $data->hasotherfields = true;
            $endorsement = $this->badge->get_endorsement();
            $endorsement->issueremail = obfuscate_mailto($endorsement->issueremail, $endorsement->issueremail);
            $data->endorsement = (array) $endorsement;
        }

        // Field: Related badges.
        $relatedbadges = $this->badge->get_related_badges(true);
        if (!empty($relatedbadges)) {
            $data->hasotherfields = true;
            $data->hasrelatedbadges = true;
            $data->relatedbadges = [];
            foreach ($relatedbadges as $related) {
                if (isloggedin() && !is_guest($this->context)) {
                    $related->url = (new moodle_url('/badges/overview.php', ['id' => $related->id]))->out(false);
                }
                $data->relatedbadges[] = (array)$related;
            }
        }

        // Field: Alignments.
        $alignments = $this->badge->get_alignments();
        if (!empty($alignments)) {
            $data->hasotherfields = true;
            $data->hasalignments = true;
            $data->alignments = [];
            foreach ($alignments as $alignment) {
                $data->alignments[] = (array)$alignment;
            }
        }

        // Field: Tags.
        $tags = \core_tag_tag::get_item_tags('core_badges', 'badge', $this->badgeid);
        $taglist = new \core_tag\output\taglist($tags);
        $data->badgetag = $taglist->export_for_template($output);

        return $data;
    }
}
