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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Final task that provides all the final steps necessary in order to finish one
 * restore like gradebook, interlinks... apart from some final cleaning
 *
 * TODO: Finish phpdocs
 */
class restore_final_task extends restore_task {

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // Review all the block_position records in backup_ids in order
        // match them now that all the contexts are created populating DB
        // as needed. Only if we are restoring blocks.
        if ($this->get_setting_value('blocks')) {
            $this->add_step(new restore_review_pending_block_positions('review_block_positions'));
        }

        // TODO: Gradebook
        // TODO: interlinks

        // Clean the temp dir (conditionally) and drop temp table
        $this->add_step(new restore_drop_and_clean_temp_stuff('drop_and_clean_temp_stuff'));

        $this->built = true;
    }

// Protected API starts here

    /**
     * Define the common setting that any restore type will have
     */
    protected function define_settings() {
        // This task has not settings (could have them, like destination or so in the future, let's see)
    }
}
