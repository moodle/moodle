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

namespace qbank_editquestion\output;

use qbank_editquestion\editquestion_helper;
use renderer_base;

/**
 * Create new question button
 *
 * @package   qbank_editquestion
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_new_question implements \renderable, \templatable {
    /** @var int $categoryid The ID of the category the quesiton will be added to. */
    protected int $categoryid;

    /** @var array $params URL parameters to pass to the add question form. */
    protected array $params;

    /** @var bool $canadd True if the add question button should be displayed. If false, a placeholder will be shown.*/
    protected bool $canadd;

    /**
     * Store data for building the template context.
     *
     * @param int $categoryid
     * @param array $params
     * @param bool $canadd
     */
    public function __construct(int $categoryid, array $params, bool $canadd) {
        $this->categoryid = $categoryid;
        $this->params = $params;
        $this->canadd = $canadd;
    }

    public function export_for_template(renderer_base $output): array {
        $addquestiondisplay = [];
        $addquestiondisplay['canadd'] = $this->canadd;
        if ($this->canadd) {
            $this->params['category'] = $this->categoryid;
            $url = new \moodle_url('/question/bank/editquestion/addquestion.php', $this->params);
            $addquestiondisplay['buttonhtml'] = $output->single_button(
                $url,
                get_string('createnewquestion', 'question'),
                'get',
                ['disabled' => 'disabled'],
            );
            $addquestiondisplay['qtypeform'] = editquestion_helper::print_choose_qtype_to_add_form([]);
        }
        return $addquestiondisplay;
    }
}
