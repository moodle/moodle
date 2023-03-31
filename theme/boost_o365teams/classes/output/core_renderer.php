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

namespace theme_boost_o365teams\output;

use coding_exception;
use html_writer;
use tabobject;
use tabtree;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use paging_bar;
use context_course;
use pix_icon;

defined('MOODLE_INTERNAL') || die;

/**
 * Core Render.
 *
 * @package    theme_boost_o365teams
 * @copyright  2018 Enovation Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    /**
     * Return header html.
     * The header section includes custom content security policy setting, as well as reference to the Microsoft Teams JS lib.
     *
     * @return string
     */
    public function standard_head_html() {
        $output = parent::standard_head_html();

        $output .= "<meta http-equiv=\"Content-Security-Policy\" content=\"default-src *; style-src 'self'
                   'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://statics.teams.microsoft.com;
                    font-src data: *\">\n";
        $output .= "<script src=\"https://statics.teams.microsoft.com/sdk/v1.0/js/MicrosoftTeams.min.js\"></script>\n";

        return $output;
    }

    /**
     * Return HTML that shows link to the profile page of the user who is logged in.
     *
     * @return string
     */
    public function user_link() {
        global $USER, $OUTPUT;

        if (!empty($USER->id)) {

            $profilepagelink = new moodle_url('/user/profile.php', ['id' => $USER->id]);
            $profilepic = $OUTPUT->user_picture($USER, ['size' => 26, 'link' => false]);
            $userfullname = fullname($USER);
            $piclink = html_writer::link($profilepagelink, $profilepic, ['target' => '_blank', 'class' => 'user_details']);
            $userprofile = html_writer::link($profilepagelink, $userfullname, ['target' => '_blank']);

            return $piclink . $userprofile;
        } else {
            return "";
        }
    }

    /**
     * Return page footer.
     * Page footer contains JS calls to the Microsoft Teams JS lib.
     *
     * @return string
     */
    public function footer() {
        $footer = parent::footer();

        $js = 'microsoftTeams.initialize();';
        $footer .= html_writer::script($js);

        return $footer;
    }

    /**
     * Return HTML for feedback link.
     *
     * @return string
     */
    public function feedback_link() {
        $feedbacklink = '';

        // Hardcoded URL.
        $feedbacklinksetting = 'https://microsoftteams.uservoice.com/forums/916759-moodle';
        if ($feedbacklinksetting) {
            $feedbacklink = html_writer::link($feedbacklinksetting, html_writer::tag('span',
                get_string('feedback', 'theme_boost_o365teams')), ['target' => '_blank', 'class' => 'feedbacklink',
                'title' => get_string('share_feedback', 'theme_boost_o365teams')]);
        }

        return $feedbacklink;
    }

    /**
     * Return page footer stamp.
     * Stamp is from user upload in theme settings, or Moodle logo if not uploaded.
     *
     * @return string
     */
    public function get_footer_stamp() {
        global $CFG, $OUTPUT, $PAGE;

        if (!empty($PAGE->theme->setting_file_url('footer_stamp', 'footer_stamp'))) {
            $fileurl = $PAGE->theme->setting_file_url('footer_stamp', 'footer_stamp');
            // Get a URL suitable for moodle_url.
            $relativebaseurl = preg_replace('|^https?://|i', '//', $CFG->wwwroot);
            $relativefileurl = str_replace($relativebaseurl, '', $fileurl);
            $url = new moodle_url($relativefileurl);
            $img = html_writer::empty_tag('img', ["src" => $url]);

            $coursepageurl = $this->page->url;
            $stamp = html_writer::link($coursepageurl, $img, ['target' => '_blank', 'class' => 'stamp']);
        } else {
            $img = html_writer::empty_tag('img', ["src" => $OUTPUT->image_url('moodlelogo', 'theme')]);

            $coursepageurl = $this->page->url;
            $stamp = html_writer::link($coursepageurl, $img, ['target' => '_blank', 'class' => 'stamp']);
        }

        return $stamp;
    }
}
