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
 * Datasets manager.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Datasets manager.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataset_manager {

    /**
     * File area for labelled datasets.
     */
    const LABELLED_FILEAREA = 'labelled';

    /**
     * File area for unlabelled datasets.
     */
    const UNLABELLED_FILEAREA = 'unlabelled';

    /**
     * File area for exported datasets.
     */
    const EXPORT_FILEAREA = 'export';

    /**
     * Evaluation file file name.
     */
    const EVALUATION_FILENAME = 'evaluation.csv';

    /**
     * The model id.
     *
     * @var int
     */
    protected $modelid;

    /**
     * Range processor in use.
     *
     * @var string
     */
    protected $timesplittingid;

    /**
     * @var int
     */
    protected $analysableid;

    /**
     * Whether this is a dataset for evaluation or not.
     *
     * @var bool
     */
    protected $evaluation;

    /**
     * The dataset filearea. Must be one of the self::*_FILEAREA options.
     *
     * @var string
     */
    protected $filearea;

    /**
     * Constructor method.
     *
     * @throws \coding_exception
     * @param int $modelid
     * @param int $analysableid
     * @param string $timesplittingid
     * @param string $filearea
     * @param bool $evaluation
     * @return void
     */
    public function __construct($modelid, $analysableid, $timesplittingid, $filearea, $evaluation = false) {

        if ($filearea !== self::EXPORT_FILEAREA && $filearea !== self::LABELLED_FILEAREA &&
                $filearea !== self::UNLABELLED_FILEAREA) {
            throw new \coding_exception('Invalid provided filearea');
        }

        $this->modelid = $modelid;
        $this->analysableid = $analysableid;
        $this->timesplittingid = $timesplittingid;
        $this->filearea = $filearea;
        $this->evaluation = $evaluation;
    }

    /**
     * Store the dataset in the internal file system.
     *
     * @param array $data
     * @return \stored_file
     */
    public function store($data) {

        // Delete previous file if it exists.
        $fs = get_file_storage();

        $filerecord = [
            'component' => 'analytics',
            'filearea' => $this->filearea,
            'itemid' => $this->modelid,
            'contextid' => \context_system::instance()->id,
            'filepath' => '/analysable/' . $this->analysableid . '/' .
                \core_analytics\analysis::clean_time_splitting_id($this->timesplittingid) . '/',
            'filename' => self::get_filename($this->evaluation)
        ];

        // Delete previous and old (we already checked that previous copies are not recent) evaluation files for this analysable.
        if ($this->evaluation) {
            $select = " = {$filerecord['itemid']} AND filepath = :filepath";
            $fs->delete_area_files_select($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'],
                $select, array('filepath' => $filerecord['filepath']));
        }

        // Write all this stuff to a tmp file.
        $filepath = make_request_directory() . DIRECTORY_SEPARATOR . $filerecord['filename'];
        $fh = fopen($filepath, 'w+');
        if (!$fh) {
            return false;
        }
        foreach ($data as $line) {
            fputcsv($fh, $line, escape: '\\');
        }
        fclose($fh);

        return $fs->create_file_from_pathname($filerecord, $filepath);
    }

    /**
     * Returns the previous evaluation file.
     *
     * Important to note that this is per modelid + timesplittingid, when dealing with multiple
     * analysables this is the merged file. Do not confuse with self::get_evaluation_analysable_file
     *
     * @param int $modelid
     * @param string $timesplittingid
     * @return \stored_file
     */
    public static function get_previous_evaluation_file($modelid, $timesplittingid) {
        $fs = get_file_storage();
        // Evaluation data is always labelled.
        $filepath = '/timesplitting/' . \core_analytics\analysis::clean_time_splitting_id($timesplittingid) . '/';
        return $fs->get_file(\context_system::instance()->id, 'analytics', self::LABELLED_FILEAREA, $modelid,
            $filepath, self::EVALUATION_FILENAME);
    }

    /**
     * Gets the list of files that couldn't be previously used for training and prediction.
     *
     * @param int $modelid
     * @param bool $includetarget
     * @param string[] $timesplittingids
     * @return null
     */
    public static function get_pending_files($modelid, $includetarget, $timesplittingids) {
        global $DB;

        $fs = get_file_storage();

        if ($includetarget) {
            $filearea = self::LABELLED_FILEAREA;
            $usedfileaction = 'trained';
        } else {
            $filearea = self::UNLABELLED_FILEAREA;
            $usedfileaction = 'predicted';
        }

        $select = 'modelid = :modelid AND action = :action';
        $params = array('modelid' => $modelid, 'action' => $usedfileaction);
        $usedfileids = $DB->get_fieldset_select('analytics_used_files', 'fileid', $select, $params);

        // Very likely that we will only have 1 time splitting method here.
        $filesbytimesplitting = array();
        foreach ($timesplittingids as $timesplittingid) {

            $filepath = '/timesplitting/' . \core_analytics\analysis::clean_time_splitting_id($timesplittingid) . '/';
            $files = $fs->get_directory_files(\context_system::instance()->id, 'analytics', $filearea, $modelid, $filepath);
            foreach ($files as $file) {

                // Discard evaluation files.
                if ($file->get_filename() === self::EVALUATION_FILENAME) {
                    continue;
                }

                // No dirs.
                if ($file->is_directory()) {
                    continue;
                }

                // Already used for training.
                if (in_array($file->get_id(), $usedfileids)) {
                    continue;
                }

                $filesbytimesplitting[$timesplittingid][] = $file;
            }
        }

        return $filesbytimesplitting;
    }

    /**
     * Deletes previous evaluation files of this model.
     *
     * @param int $modelid
     * @param string $timesplittingid
     * @return bool
     */
    public static function delete_previous_evaluation_file($modelid, $timesplittingid) {
        if ($file = self::get_previous_evaluation_file($modelid, $timesplittingid)) {
            $file->delete();
            return true;
        }

        return false;
    }

    /**
     * Returns this (model + analysable + time splitting) file.
     *
     * @param int $modelid
     * @param int $analysableid
     * @param string $timesplittingid
     * @return \stored_file
     */
    public static function get_evaluation_analysable_file($modelid, $analysableid, $timesplittingid) {

        // Delete previous file if it exists.
        $fs = get_file_storage();

        // Always evaluation.csv and labelled as it is an evaluation file.
        $filearea = self::LABELLED_FILEAREA;
        $filename = self::get_filename(true);
        $filepath = '/analysable/' . $analysableid . '/' .
            \core_analytics\analysis::clean_time_splitting_id($timesplittingid) . '/';
        return $fs->get_file(\context_system::instance()->id, 'analytics', $filearea, $modelid, $filepath, $filename);
    }

    /**
     * Merge multiple files into one.
     *
     * Important! It is the caller responsability to ensure that the datasets are compatible.
     *
     * @param array  $files
     * @param int    $modelid
     * @param string $timesplittingid
     * @param string $filearea
     * @param bool   $evaluation
     * @return \stored_file
     */
    public static function merge_datasets(array $files, $modelid, $timesplittingid, $filearea, $evaluation = false) {

        $tmpfilepath = make_request_directory() . DIRECTORY_SEPARATOR . 'tmpfile.csv';

        // Add headers.
        // We could also do this with a single iteration gathering all files headers and appending them to the beginning of the file
        // once all file contents are merged.
        $varnames = '';
        $analysablesvalues = array();
        foreach ($files as $file) {
            $rh = $file->get_content_file_handle();

            // Copy the var names as they are, all files should have the same var names.
            $varnames = fgetcsv($rh, escape: '\\');

            $analysablesvalues[] = fgetcsv($rh, escape: '\\');

            // Copy the columns as they are, all files should have the same columns.
            $columns = fgetcsv($rh, escape: '\\');
        }

        // Merge analysable values skipping the ones that are the same in all analysables.
        $values = array();
        foreach ($analysablesvalues as $analysablevalues) {
            foreach ($analysablevalues as $varkey => $value) {
                // Sha1 to make it unique.
                $values[$varkey][sha1($value)] = $value;
            }
        }
        foreach ($values as $varkey => $varvalues) {
            $values[$varkey] = implode('|', $varvalues);
        }

        // Start writing to the merge file.
        $wh = fopen($tmpfilepath, 'w');
        if (!$wh) {
            throw new \moodle_exception('errorcannotwritedataset', 'analytics', '', $tmpfilepath);
        }

        fputcsv($wh, $varnames, escape: '\\');
        fputcsv($wh, $values, escape: '\\');
        fputcsv($wh, $columns, escape: '\\');

        // Iterate through all files and add them to the tmp one. We don't want file contents in memory.
        foreach ($files as $file) {
            $rh = $file->get_content_file_handle();

            // Skip headers.
            fgets($rh);
            fgets($rh);
            fgets($rh);

            // Copy all the following lines.
            while ($line = fgets($rh)) {
                fwrite($wh, $line);
            }
            fclose($rh);
        }
        fclose($wh);

        $filerecord = [
            'component' => 'analytics',
            'filearea' => $filearea,
            'itemid' => $modelid,
            'contextid' => \context_system::instance()->id,
            'filepath' => '/timesplitting/' . \core_analytics\analysis::clean_time_splitting_id($timesplittingid) . '/',
            'filename' => self::get_filename($evaluation)
        ];

        $fs = get_file_storage();

        return $fs->create_file_from_pathname($filerecord, $tmpfilepath);
    }

    /**
     * Exports the model training data.
     *
     * @param int $modelid
     * @param string $timesplittingid
     * @return \stored_file|false
     */
    public static function export_training_data($modelid, $timesplittingid) {

        $fs = get_file_storage();

        $contextid = \context_system::instance()->id;
        $filepath = '/timesplitting/' . \core_analytics\analysis::clean_time_splitting_id($timesplittingid) . '/';

        $files = $fs->get_directory_files($contextid, 'analytics', self::LABELLED_FILEAREA, $modelid,
            $filepath, true, false);

        // Discard evaluation files.
        foreach ($files as $key => $file) {
            if ($file->get_filename() === self::EVALUATION_FILENAME) {
                unset($files[$key]);
            }
        }

        if (empty($files)) {
            return false;
        }

        return self::merge_datasets($files, $modelid, $timesplittingid, self::EXPORT_FILEAREA);
    }

    /**
     * Returns the dataset file data structured by sampleids using the indicators and target column names.
     *
     * @param \stored_file $dataset
     * @return array
     */
    public static function get_structured_data(\stored_file $dataset) {

        if ($dataset->get_filearea() !== 'unlabelled') {
            throw new \coding_exception('Sorry, only support for unlabelled data');
        }

        $rh = $dataset->get_content_file_handle();

        // Skip dataset info.
        fgets($rh);
        fgets($rh);

        $calculations = array();

        $headers = fgetcsv($rh, escape: '\\');
        // Get rid of the sampleid column name.
        array_shift($headers);

        while ($columns = fgetcsv($rh, escape: '\\')) {
            $uniquesampleid = array_shift($columns);

            // Unfortunately fgetcsv does not respect line's var types.
            $calculations[$uniquesampleid] = array_map(function($value) {

                if ($value === '') {
                    // We really want them as null because converted to float become 0
                    // and we need to treat the values separately.
                    return null;
                } else if (is_numeric($value)) {
                    return floatval($value);
                }
                return $value;
            }, array_combine($headers, $columns));
        }

        return $calculations;
    }

    /**
     * Delete all files of a model.
     *
     * @param int $modelid
     * @return bool
     */
    public static function clear_model_files($modelid) {
        $fs = get_file_storage();
        return $fs->delete_area_files(\context_system::instance()->id, 'analytics', false, $modelid);
    }

    /**
     * Returns the file name to be used.
     *
     * @param strinbool $evaluation
     * @return string
     */
    protected static function get_filename($evaluation) {

        if ($evaluation === true) {
            $filename = self::EVALUATION_FILENAME;
        } else {
            // Incremental time, the lock will make sure we don't have concurrency problems.
            $filename = microtime(true) . '.csv';
        }

        return $filename;
    }
}
