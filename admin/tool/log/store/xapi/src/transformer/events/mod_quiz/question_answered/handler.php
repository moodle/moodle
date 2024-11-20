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
 * Generic quiz handler for the question answered event.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_quiz\question_answered;

use src\transformer\utils as utils;

/**
 * Generic handler for quiz question answered event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @param \stdClass $questionattempt The questionattempt object.
 * @return array
 */
function handler(array $config, \stdClass $event, \stdClass $questionattempt) {
    $repo = $config['repo'];
    $question = $repo->read_record_by_id('question', $questionattempt->questionid);

    switch ($question->qtype) {
        case 'essay':
            return essay($config, $event, $questionattempt, $question);
        case 'gapselect':
            return gapselect($config, $event, $questionattempt, $question);
        case 'truefalse':
            return truefalse($config, $event, $questionattempt, $question);
        case 'randomsamatch':
            return randomsamatch($config, $event, $questionattempt, $question);
        case 'shortanswer':
            return shortanswer($config, $event, $questionattempt, $question);
        case 'match':
            return matching($config, $event, $questionattempt, $question);
        case 'multichoice':
        case 'multichoiceset':
            return multichoice($config, $event, $questionattempt, $question);
        case 'numerical':
            return numerical($config, $event, $questionattempt, $question);
        default:
            return [];
    }
}
