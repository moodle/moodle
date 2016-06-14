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
 * Functions common to the question usage statistics code.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Question statistics cron code. Deletes cached stats more than a certain age.
 */
function question_usage_statistics_cron() {
    global $DB;

    $expiretime = time() - 5 * HOURSECS;

    mtrace("\n  Cleaning up old question statistics cache records...", '');

    $DB->delete_records_select('question_statistics', 'timemodified < ?', array($expiretime));
    $responseanlysisids = $DB->get_records_select_menu('question_response_analysis',
                                                           'timemodified < ?',
                                                           array($expiretime),
                                                           'id',
                                                           'id, id AS id2');

    $DB->delete_records_list('question_response_analysis', 'id', $responseanlysisids);
    $DB->delete_records_list('question_response_count', 'analysisid', $responseanlysisids);

    mtrace('done.');
    return true;
}
