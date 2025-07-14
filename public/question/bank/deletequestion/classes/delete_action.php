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
 * Action to delete (or hide) a question, or restore a previously hidden question.
 *
 * @package   qbank_deletequestion
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_deletequestion;

use core_question\local\bank\question_version_status;
use core_question\local\bank\question_action_base;

/**
 * Action to delete (or hide) a question, or restore a previously hidden question.
 *
 * @package   qbank_deletequestion
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_action extends question_action_base {

    /**
     * @var string $strdelete
     */
    protected $strdelete;

    /**
     * @var string $strrestore
     */
    protected $strrestore;

    /**
     * Contains the url of the delete question page.
     * @var \moodle_url|string
     */
    public $deletequestionurl;

    /**
     * Array of the return parameters.
     * @var array $returnparams
     */
    protected $returnparams;

    public function init(): void {
        parent::init();
        $this->strdelete = get_string('delete');
        $this->strrestore = get_string('restore');
        $this->deletequestionurl = new \moodle_url('/question/bank/deletequestion/delete.php');
        $this->returnparams['cmid'] = $this->qbank->cm->id;

        if (!empty($this->qbank->returnurl)) {
            $this->returnparams['returnurl'] = $this->qbank->returnurl;
        }
    }

    public function get_menu_position(): int {
        return 2000;
    }

    protected function get_url_icon_and_label(\stdClass $question): array {
        if (!question_has_capability_on($question, 'edit')) {
            return [null, null, null];
        }
        if ($question->status === question_version_status::QUESTION_STATUS_HIDDEN) {
            $hiddenparams = array(
                    'unhide' => $question->id,
                    'sesskey' => sesskey());
            $hiddenparams = array_merge($hiddenparams, $this->returnparams);
            $url = new \moodle_url($this->deletequestionurl, $hiddenparams);
            return [$url, 't/restore', $this->strrestore];
        } else {
            $deleteparams = array(
                    'deleteselected' => $question->id,
                    'q' . $question->id => 1,
                    'sesskey' => sesskey());
            $deleteparams = array_merge($deleteparams, $this->returnparams);
            if (!$this->qbank->is_listing_specific_versions()) {
                $deleteparams['deleteall'] = 1;
            }
            $url = new \moodle_url($this->deletequestionurl, $deleteparams);
            return [$url, 't/delete', $this->strdelete];
        }
    }

    /**
     * Override method to get url and label for delete action to add the text-danger class.
     *
     * @param \stdClass $question
     * @return \action_menu_link|null
     */
    public function get_action_menu_link(\stdClass $question): ?\action_menu_link {
        $deletelink = parent::get_action_menu_link($question);
        if ($deletelink !== null) {
            $deletelink->add_class('text-danger');
        }
        return $deletelink;
    }
}
