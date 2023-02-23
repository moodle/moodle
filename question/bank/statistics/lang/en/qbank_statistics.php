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
 * Strings for component qbank_statistics, language 'en'
 *
 * @package    qbank_statistics
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Question statistics';
$string['privacy:metadata'] = 'The Question statistics question bank plugin does not store any personal data.';

// Columns.
$string['facility_index'] = 'Facility index';
$string['facility_index_help'] = 'The facility index gives the average mark (as a percentage) obtained on the question (all versions) in all quizzes where the question has been attempted. A higher value normally indicates an easier question.';
$string['discriminative_efficiency'] = 'Discriminative efficiency';
$string['discriminative_efficiency_help'] = 'Discriminative efficiency is a statistical estimate of how well the question assesses students, with a higher value being better. A particularly low value may indicate a problem with the question. A very difficult or easy question (with facility index close to 0% or 100%) can also lead to a low value.';
$string['discriminative_efficiency_link'] = 'mod/quiz/statistics';
$string['discrimination_index'] = 'Needs checking?';
$string['discrimination_index_help'] = 'A question is indicated as likely to need checking based on question statistics. For example, if students obtain a low score on the question but a high score on the whole quiz, or a high score on the question but a low score on the whole quiz, then there may be a problem with the question such as the wrong answer being set as correct. Statistics are not infallible though; this is just a hint that the question should be checked.';

// Text format.
$string['verylikely'] = 'Very likely';
$string['likely'] = 'Likely';
$string['unlikely'] = 'Unlikely';
$string['na'] = 'N/A';
$string['emptyvalue'] = '-';
