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
 * Keeps track of the analysis results by storing the results in files.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\analysis;

defined('MOODLE_INTERNAL') || die();

/**
 * Keeps track of the analysis results by storing the results in files.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class result_file extends result {

    /**
     * Stores the analysis results by time-splitting method.
     * @var array
     */
    private $filesbytimesplitting = [];

    /**
     * Stores the analysis results.
     * @param  array $results
     * @return bool            True if anything was successfully analysed
     */
    public function add_analysable_results(array $results): bool {

        $any = false;

        // Process all provided time splitting methods.
        foreach ($results as $timesplittingid => $result) {
            if (!empty($result->result)) {
                $this->filesbytimesplitting[$timesplittingid][] = $result->result;
                $any = true;
            }
        }

        if (empty($any)) {
            return false;
        }
        return true;
    }

    /**
     * Retrieves cached results during evaluation.
     *
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @param  \core_analytics\analysable                $analysable
     * @return mixed A \stored_file in this case.
     */
    public function retrieve_cached_result(\core_analytics\local\time_splitting\base $timesplitting,
        \core_analytics\analysable $analysable) {

        // For evaluation purposes we don't need to be that strict about how updated the data is,
        // if this analyser was analysed less that 1 week ago we skip generating a new one. This
        // helps scale the evaluation process as sites with tons of courses may need a lot of time to
        // complete an evaluation.
        if (!empty($options['evaluation']) && !empty($options['reuseprevanalysed'])) {

            $previousanalysis = \core_analytics\dataset_manager::get_evaluation_analysable_file($this->analyser->get_modelid(),
                $analysable->get_id(), $timesplitting->get_id());
            // 1 week is a partly random time interval, no need to worry about DST.
            $boundary = time() - WEEKSECS;
            if ($previousanalysis && $previousanalysis->get_timecreated() > $boundary) {
                // Recover the previous analysed file and avoid generating a new one.
                return $previousanalysis;
            }
        }

        return false;
    }

    /**
     * Formats the result.
     *
     * @param  array                                     $data
     * @param  \core_analytics\local\target\base         $target
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @param  \core_analytics\analysable                $analysable
     * @return mixed A \stored_file in this case
     */
    public function format_result(array $data, \core_analytics\local\target\base $target,
            \core_analytics\local\time_splitting\base $timesplitting, \core_analytics\analysable $analysable) {

        if (!empty($this->includetarget)) {
            $filearea = \core_analytics\dataset_manager::LABELLED_FILEAREA;
        } else {
            $filearea = \core_analytics\dataset_manager::UNLABELLED_FILEAREA;
        }
        $dataset = new \core_analytics\dataset_manager($this->modelid, $analysable->get_id(),
            $timesplitting->get_id(), $filearea, $this->options['evaluation']);

        // Add extra metadata.
        $this->add_model_metadata($data, $timesplitting, $target);

        // Write all calculated data to a file.
        if (!$result = $dataset->store($data)) {
            return false;
        }

        return $result;
    }

    /**
     * Returns the results of the analysis.
     * @return array
     */
    public function get(): array {

        if ($this->options['evaluation'] === false) {
            // Look for previous training and prediction files we generated and couldn't be used
            // by machine learning backends because they weren't big enough.

            $pendingfiles = \core_analytics\dataset_manager::get_pending_files($this->modelid, $this->includetarget,
                array_keys($this->filesbytimesplitting));
            foreach ($pendingfiles as $timesplittingid => $files) {
                foreach ($files as $file) {
                    $this->filesbytimesplitting[$timesplittingid][] = $file;
                }
            }
        }

        // We join the datasets by time splitting method.
        $timesplittingfiles = array();
        foreach ($this->filesbytimesplitting as $timesplittingid => $files) {

            if ($this->options['evaluation'] === true) {
                // Delete the previous copy. Only when evaluating.
                \core_analytics\dataset_manager::delete_previous_evaluation_file($this->modelid, $timesplittingid);
            }

            // Merge all course files into one.
            if ($this->includetarget) {
                $filearea = \core_analytics\dataset_manager::LABELLED_FILEAREA;
            } else {
                $filearea = \core_analytics\dataset_manager::UNLABELLED_FILEAREA;
            }
            $timesplittingfiles[$timesplittingid] = \core_analytics\dataset_manager::merge_datasets($files,
                $this->modelid, $timesplittingid, $filearea, $this->options['evaluation']);
        }

        if (!empty($pendingfiles)) {
            // We must remove them now as they are already part of another dataset.
            foreach ($pendingfiles as $timesplittingid => $files) {
                foreach ($files as $file) {
                    $file->delete();
                }
            }
        }

        return $timesplittingfiles;
    }

    /**
     * Adds target metadata to the dataset.
     *
     * The final dataset document will look like this:
     * ----------------------------------------------------
     * metadata1,metadata2,metadata3,.....
     * value1, value2, value3,.....
     *
     * header1,header2,header3,header4,.....
     * stud1value1,stud1value2,stud1value3,stud1value4,.....
     * stud2value1,stud2value2,stud2value3,stud2value4,.....
     * .....
     * ----------------------------------------------------
     *
     * @param array $data
     * @param \core_analytics\local\time_splitting\base $timesplitting
     * @param \core_analytics\local\target\base         $target
     * @return null
     */
    private function add_model_metadata(array &$data, \core_analytics\local\time_splitting\base $timesplitting,
            \core_analytics\local\target\base $target) {
        global $CFG;

        // If no target the first column is the sampleid, if target the last column is the target.
        // This will need to be updated when we support unsupervised learning models.
        $metadata = array(
            'timesplitting' => $timesplitting->get_id(),
            'nfeatures' => count(current($data)) - 1,
            'moodleversion' => $CFG->version,
            'targetcolumn' => $target->get_id()
        );
        if ($target->is_linear()) {
            $metadata['targettype'] = 'linear';
            $metadata['targetmin'] = $target::get_min_value();
            $metadata['targetmax'] = $target::get_max_value();
        } else {
            $metadata['targettype'] = 'discrete';
            $metadata['targetclasses'] = json_encode($target::get_classes());
        }

        // The first 2 samples will be used to store metadata about the dataset.
        $metadatacolumns = [];
        $metadatavalues = [];
        foreach ($metadata as $key => $value) {
            $metadatacolumns[] = $key;
            $metadatavalues[] = $value;
        }

        // This will also reset samples' dataset keys.
        array_unshift($data, $metadatacolumns, $metadatavalues);
    }
}
