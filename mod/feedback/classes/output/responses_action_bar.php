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

namespace mod_feedback\output;

use moodle_url;
use url_select;

/**
 * Class responses_action_bar. The tertiary nav for the responses page
 *
 * @copyright 2021 Peter Dias
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */
class responses_action_bar extends base_action_bar {
    /** @var moodle_url $currenturl The current page url */
    private $currenturl;

    /**
     * responses_action_bar constructor.
     *
     * @param int $cmid The cmid for the module we are operating on
     * @param moodle_url $pageurl The current page url
     */
    public function __construct(int $cmid, moodle_url $pageurl) {
        parent::__construct($cmid);
        $this->currenturl = $pageurl;
        $this->urlparams['courseid'] = $this->course->id;
    }

    /**
     * Return the items to be used in the tertiary nav
     *
     * @return array
     */
    public function get_items(): array {
        $items = [];
        if (has_capability('mod/feedback:viewreports', $this->context)) {
            $reporturl = new moodle_url('/mod/feedback/show_entries.php', $this->urlparams);
            $options[$reporturl->out(false)] = get_string('show_entries', 'feedback');
            $selected = $this->currenturl->compare($reporturl, URL_MATCH_BASE) ? $reporturl : $this->currenturl;

            if ($this->feedback->anonymous == FEEDBACK_ANONYMOUS_NO && $this->course != SITEID) {
                $nonrespondenturl = new moodle_url('/mod/feedback/show_nonrespondents.php', $this->urlparams);
                $options[$nonrespondenturl->out(false)] = get_string('show_nonrespondents', 'feedback');
                $selected = $this->currenturl->compare($nonrespondenturl, URL_MATCH_BASE) ? $nonrespondenturl : $this->currenturl;;
            }

            // Don't show the dropdown if it's only a single item.
            if (count($options) != 1) {
                $items['left'][]['urlselect'] = new url_select($options,
                    $selected->out(false),
                    null);
            }
        }
        return $items;
    }
}
