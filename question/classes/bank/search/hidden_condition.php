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
 * A search class to control whether hidden / deleted questions are hidden in the list.
 *
 * @package   core_question
 * @copyright 2013 Ray Morris
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\bank\search;

use core_question\local\bank\question_version_status;

/**
 * This class controls whether hidden / deleted questions are hidden in the list.
 *
 * @copyright 2013 Ray Morris
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hidden_condition extends condition {
    /** @var bool Whether to include old "deleted" questions. */
    protected $hide;

    /** @var string SQL fragment to add to the where clause. */
    protected $where;

    /**
     * Constructor.
     * @param bool $hide whether to include old "deleted" questions.
     */
    public function __construct($hide = true) {
        $this->hide = $hide;
        if ($hide) {
            $this->where = "qv.status = '" . question_version_status::QUESTION_STATUS_READY . "' " .
                " OR qv.status = '" . question_version_status::QUESTION_STATUS_DRAFT . "' ";
        }
    }

    /**
     * SQL fragment to add to the where clause.
     *
     * @return string
     */
    public function where() {
        return  $this->where;
    }

    /**
     * Print HTML to display the "Also show old questions" checkbox
     */
    public function display_options_adv() {
        global $PAGE;
        $displaydata = [];
        if (!$this->hide) {
            $displaydata['checked'] = 'checked="true"';
        }
        return $PAGE->get_renderer('core_question', 'bank')->render_hidden_condition_advanced($displaydata);
    }
}
