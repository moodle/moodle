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

declare(strict_types=1);

namespace qtype_calculated;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/numerical/question.php');

/**
 * Class to represent a calculated question answer.
 *
 * @package   qtype_calculated
 * @copyright 2023 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_calculated_answer extends \qtype_numerical_answer {
    /** @var int The length of the correct answer. */
    public $correctanswerlength;

    /** @var int The format of the correct answer. */
    public $correctanswerformat;

    /** @var int The max tolerance of the correct answer. */
    public $max;

    /** @var int The min tolerance of the correct answer. */
    public $min;
}
