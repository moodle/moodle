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
 * Provides a data merger for assign submissions.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\merger\duplicateddata;

use tool_mergeusers\local\merger\finder\assign_submission_finder;
use tool_mergeusers\local\merger\finder\assign_submission_db_finder;

/**
 * Provides a data merger for assign submissions.
 *
 * @package   tool_mergeusers
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 onwards Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_duplicated_data_merger implements duplicated_data_merger {
    // This constants are located at mod/assign/locallib.php. We copy here to avoid loading full locallib.php file.
    /** @var string[] Submission states with content. */
    const ASSIGN_SUBMISSION_WITH_CONTENT = [
        'submitted',
        'draft',
        'reopened',
    ];
    /** @var string submission state without content. */
    const ASSIGN_SUBMISSION_NEW = 'new';

    /** @var assign_submission_finder finder of assign submissions. */
    private assign_submission_finder $assignsubmissionfinder;

    /**
     * Initializes the instance.
     *
     * @param assign_submission_finder|null $finder finder of assign submissions.
     */
    public function __construct(?assign_submission_finder $finder = null) {
        $this->assignsubmissionfinder = $finder ?? new assign_submission_db_finder();
    }

    /**
     * Provides the list of records ids to delete and update for the given assign submissions.
     *
     * @param object $oldsubmission submission from the user to remove.
     * @param object $newsubmission submission from the user to keep.
     * @return duplicated_data
     */
    public function merge(object $oldsubmission, object $newsubmission): duplicated_data {
        if ($this->old_submission_has_content_and_new_has_no_content($oldsubmission, $newsubmission)) {
            return duplicated_data::from_remove_and_update(
                array_keys($this->assignsubmissionfinder->all_from_assign_and_user(
                    $newsubmission->assignment,
                    $newsubmission->userid
                )),
                array_keys($this->assignsubmissionfinder->all_from_assign_and_user(
                    $oldsubmission->assignment,
                    $oldsubmission->userid
                ))
            );
        }

        if ($this->both_submissions_have_content($oldsubmission, $newsubmission)) {
            $submissiontomodify = $newsubmission;
            $submissiontoremove = $oldsubmission;
            if ($this->old_user_submission_is_older_than_new_user_submission($oldsubmission, $newsubmission)) {
                $submissiontomodify = $oldsubmission;
                $submissiontoremove = $newsubmission;
            }
            $modifyid = $this->assignsubmissionfinder->all_from_assign_and_user(
                $submissiontomodify->assignment,
                $submissiontomodify->userid
            );
            $removeid = $this->assignsubmissionfinder->all_from_assign_and_user(
                $submissiontoremove->assignment,
                $submissiontoremove->userid
            );

            return duplicated_data::from_remove_and_update(array_keys($removeid), array_keys($modifyid));
        }

        return duplicated_data::from_remove(
            array_keys($this->assignsubmissionfinder->all_from_assign_and_user(
                $oldsubmission->assignment,
                $oldsubmission->userid
            ))
        );
    }

    /**
     * Informs whether the submission from the user to remove is the only that has content.
     *
     * @param object $oldsubmission submission from the user remove.
     * @param object $newsubmission submission from the user to keep.
     * @return bool
     */
    private function old_submission_has_content_and_new_has_no_content(object $oldsubmission, object $newsubmission): bool {
        return in_array($oldsubmission->status, self::ASSIGN_SUBMISSION_WITH_CONTENT, true) &&
            $newsubmission->status == self::ASSIGN_SUBMISSION_NEW;
    }

    /**
     * Informs if both users have content on their submissions.
     *
     * @param object $oldsubmission submission from the user remove.
     * @param object $newsubmission submission from the user to keep.
     * @return bool
     */
    private function both_submissions_have_content(object $oldsubmission, object $newsubmission): bool {
        return in_array($oldsubmission->status, self::ASSIGN_SUBMISSION_WITH_CONTENT) &&
            in_array($newsubmission->status, self::ASSIGN_SUBMISSION_WITH_CONTENT);
    }

    /**
     * Informs whether the submission from the user to remove is older than the submission from the user to remove.
     * @param object $oldsubmission submission from the user remove.
     * @param object $newsubmission submission from the user to keep.
     * @return bool
     */
    private function old_user_submission_is_older_than_new_user_submission(object $oldsubmission, object $newsubmission): bool {
        return $oldsubmission->timemodified <= $newsubmission->timemodified;
    }
}
