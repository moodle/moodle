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

namespace qbank_viewquestiontext;

use core\output\datafilter;
use core_question\local\bank\condition;
use core_question\local\bank\view;

/**
 * This class controls from which category questions are listed.
 *
 * @package    qbank_viewquestiontext
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questiontext_condition extends condition {

    /**
     * @var int Question text is off.
     */
    const OFF = 0;

    /**
     * @var int Question text is displayed in plain text mode.
     */
    const PLAIN = 1;

    /**
     * @var int Question text is displayed fully rendered.
     */
    const FULL = 2;

    /**
     * @var ?int $showtext Initial value of the filter, for determining the pre-selected option.
     */
    protected ?int $showtext = null;

    public function __construct(view $qbank = null) {
        if (is_null($qbank)) {
            return;
        }
        $filter = $qbank->get_pagevars('filter');
        if (isset($filter['showtext'])) {
            $this->showtext = (int)$filter['showtext']['values'][0];
        }
    }

    public function allow_custom() {
        return false;
    }

    public function allow_multiple() {
        return false;
    }

    public static function get_condition_key() {
        return 'showtext';
    }

    public function get_title() {
        return get_string('showquestiontext', 'core_question');
    }

    public function get_join_list(): array {
        return [
            datafilter::JOINTYPE_ANY,
        ];
    }

    public function get_filter_class() {
        return 'qbank_viewquestiontext/datafilter/filtertypes/showtext';
    }

    public function get_initial_values() {
        return [
            [
                'value' => self::OFF,
                'title' => get_string('showquestiontext_off', 'question'),
                'selected' => $this->showtext === self::OFF,
            ],
            [
                'value' => self::PLAIN,
                'title' => get_string('showquestiontext_plain', 'question'),
                'selected' => $this->showtext === self::PLAIN,
            ],
            [
                'value' => self::FULL,
                'title' => get_string('showquestiontext_full', 'question'),
                'selected' => $this->showtext === self::FULL,
            ]
        ];
    }
}
