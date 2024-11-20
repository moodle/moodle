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
 * Generic feedback handler for transforming feedback events.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_feedback\item_answered;

use src\transformer\utils as utils;

/**
 * Generic handler for the mod_feedback item answered event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @param \stdClass $feedbackvalue The value of the feedback type.
 * @return array
 */
function handler(array $config, \stdClass $event, \stdClass $feedbackvalue) {
    $repo = $config['repo'];
    $feedbackitem = $repo->read_record_by_id('feedback_item', $feedbackvalue->item);

    switch ($feedbackitem->typ) {
        case 'multichoicerated':
            return multichoicerated($config, $event, $feedbackvalue, $feedbackitem);
        case 'multichoice':
            return multichoice($config, $event, $feedbackvalue, $feedbackitem);
        case 'numeric':
            return numerical($config, $event, $feedbackvalue, $feedbackitem);
        case 'textarea':
            return textarea($config, $event, $feedbackvalue, $feedbackitem);
        case 'textfield':
            return textfield($config, $event, $feedbackvalue, $feedbackitem);
        default:
            return [];
    }
}
