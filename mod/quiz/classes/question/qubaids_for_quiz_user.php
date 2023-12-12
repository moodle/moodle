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

namespace mod_quiz\question;

/**
 * A {@see qubaid_condition} for finding all the question usages belonging to a particular user and quiz combination.
 *
 * @package   mod_quiz
 * @category  question
 * @copyright 2018 Andrew Nicols <andrwe@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated This class was never needed because qubaids_for_users_attempts already existed and is more flexible.
 */
class qubaids_for_quiz_user extends qubaids_for_users_attempts {
    /**
     * Constructor.
     *
     * @param int $quizid The quiz to search.
     * @param int $userid The user to filter on
     * @param bool $includepreviews Whether to include preview attempts
     * @param bool $onlyfinished Whether to only include finished attempts or not
     */
    public function __construct(int $quizid, int $userid,
            bool $includepreviews = true, bool $onlyfinished = false) {
        debugging('qubaids_for_quiz_user is deprecated. Please use qubaids_for_users_attempts instead.');
        parent::__construct($quizid, $userid,
                $onlyfinished ? 'finished' : 'all', $includepreviews);
    }
}
