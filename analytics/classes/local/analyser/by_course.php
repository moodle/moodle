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
 * Abstract analyser in course basis.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract analyser in course basis.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class by_course extends base {

    /**
     * Return the list of courses to analyse.
     *
     * @return \core_analytics\course[]
     */
    public function get_courses() {

        // Default to all system courses.
        if (!empty($this->options['filter'])) {
            $courses = $this->options['filter'];
        } else {
            // Iterate through all potentially valid courses.
            $courses = get_courses();
        }
        unset($courses[SITEID]);

        $analysables = array();
        foreach ($courses as $course) {
            // Skip the frontpage course.
            $analysable = \core_analytics\course::instance($course);
            $analysables[$analysable->get_id()] = $analysable;
        }

        if (empty($analysables)) {
            $this->log[] = get_string('nocourses', 'analytics');
        }

        return $analysables;
    }

    /**
     * Returns the analysed data
     *
     * @param bool $includetarget
     * @return \stored_file[]
     */
    public function get_analysable_data($includetarget) {

        $filesbytimesplitting = array();

        // This class and all children will iterate through a list of courses (\core_analytics\course).
        $analysables = $this->get_courses('all', 'c.sortorder ASC');
        foreach ($analysables as $analysableid => $analysable) {

            $files = $this->process_analysable($analysable, $includetarget);

            // Later we will need to aggregate data by time splitting method.
            foreach ($files as $timesplittingid => $file) {
                $filesbytimesplitting[$timesplittingid][$analysableid] = $file;
            }
        }

        // We join the datasets by time splitting method.
        $timesplittingfiles = $this->merge_analysable_files($filesbytimesplitting, $includetarget);

        return $timesplittingfiles;
    }

    /**
     * Merges analysable dataset files into 1.
     *
     * @param array $filesbytimesplitting
     * @param bool $includetarget
     * @return \stored_file[]
     */
    protected function merge_analysable_files($filesbytimesplitting, $includetarget) {

        $timesplittingfiles = array();
        foreach ($filesbytimesplitting as $timesplittingid => $files) {

            if ($this->options['evaluation'] === true) {
                // Delete the previous copy. Only when evaluating.
                \core_analytics\dataset_manager::delete_previous_evaluation_file($this->modelid, $timesplittingid);
            }

            // Merge all course files into one.
            if ($includetarget) {
                $filearea = \core_analytics\dataset_manager::LABELLED_FILEAREA;
            } else {
                $filearea = \core_analytics\dataset_manager::UNLABELLED_FILEAREA;
            }
            $timesplittingfiles[$timesplittingid] = \core_analytics\dataset_manager::merge_datasets($files,
                $this->modelid, $timesplittingid, $filearea, $this->options['evaluation']);
        }

        return $timesplittingfiles;
    }
}
