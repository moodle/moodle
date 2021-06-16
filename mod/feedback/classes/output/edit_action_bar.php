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
use action_link;
use single_select;
use url_select;

/**
 * Class actionbar - Display the action bar
 *
 * @package   mod_feedback
 * @copyright 2021 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_action_bar extends base_action_bar {
    /** @var moodle_url $currenturl The current page url */
    private $currenturl;
    /** @var int|null $lastposition The index of the last question type in the feedback module */
    private $lastposition;

    /**
     * edit_action_bar constructor.
     *
     * @param int $cmid The course module id
     * @param moodle_url $pageurl The current page url
     * @param int|null $lastposition Index of the last question in the feedback
     */
    public function __construct(int $cmid, moodle_url $pageurl, ?int $lastposition = null) {
        parent::__construct($cmid);
        $this->currenturl = $pageurl;
        $this->lastposition = $lastposition;
    }

    /**
     * Return the items to be used for the tertiary nav
     *
     * @return array
     */
    public function get_items(): array {
        $url = new moodle_url('/mod/feedback/view.php', ['id' => $this->cmid]);
        $items['left'][]['actionlink'] = new action_link($url, get_string('back'), null, ['class' => 'btn btn-secondary']);

        if (has_capability('mod/feedback:edititems', $this->context)) {
            $editurl = new moodle_url('/mod/feedback/edit.php', $this->urlparams);
            $templateurl = new moodle_url('/mod/feedback/manage_templates.php', $this->urlparams);
            $importurl = new moodle_url('/mod/feedback/import.php', $this->urlparams);

            $options = [
                $editurl->out(false) => get_string('add_item', 'feedback'),
                $templateurl->out(false) => get_string('using_templates', 'feedback'),
                $importurl->out(false) => get_string('import_questions', 'feedback')
            ];

            $selected = $this->currenturl;
            // Template pages can have sub pages, so match these.
            if ($this->currenturl->compare(new moodle_url('/mod/feedback/use_templ.php'), URL_MATCH_BASE)) {
                $selected = $templateurl;
            }

            $items['left'][]['urlselect'] = new url_select($options, $selected->out(false), null);

            $viewquestions = $editurl->compare($this->currenturl);
            if ($viewquestions) {
                $select = new single_select(new moodle_url('/mod/feedback/edit_item.php',
                    ['cmid' => $this->cmid, 'position' => $this->lastposition, 'sesskey' => sesskey()]),
                    'typ', feedback_load_feedback_items_options());
                $items['left'][]['singleselect'] = $select;

                $exporturl = new moodle_url('/mod/feedback/export.php', $this->urlparams + ['action' => 'exportfile']);
                $items['export'] = new action_link(
                    $exporturl,
                    get_string('export_questions', 'feedback'),
                    null,
                    ['class' => 'btn btn-secondary']);
            }
        }

        return $items;
    }
}
