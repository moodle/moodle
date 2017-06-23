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
 * Python predictions processor
 *
 * @package   mlbackend_python
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mlbackend_python;

defined('MOODLE_INTERNAL') || die();

/**
 * Python predictions processor.
 *
 * @package   mlbackend_python
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor implements \core_analytics\predictor {

    /**
     * The required version of the python package that performs all calculations.
     */
    const REQUIRED_PIP_PACKAGE_VERSION = '0.0.2';

    /**
     * Is the plugin ready to be used?.
     *
     * @return bool
     */
    public function is_ready() {

        // Check the installed pip package version.
        $cmd = 'python -m moodlemlbackend.version';

        $output = null;
        $exitcode = null;
        // Execute it sending the standard error to $output.
        $result = exec($cmd . ' 2>&1', $output, $exitcode);

        if ($result === self::REQUIRED_PIP_PACKAGE_VERSION) {
            return true;
        }

        if ($exitcode != 0) {
            return get_string('pythonpackagenotinstalled', 'mlbackend_python', $cmd);
        }

        if ($result) {
            $a = (object)array('installed' => $result, 'required' => self::REQUIRED_PIP_PACKAGE_VERSION);
            return get_string('packageinstalledshouldbe', 'mlbackend_python', $a);
        }

        return get_string('pythonpackagenotinstalled', 'mlbackend_python', $cmd);
    }

    /**
     * Trains a machine learning algorithm with the provided dataset.
     *
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function train($uniqueid, \stored_file $dataset, $outputdir) {

        // Obtain the physical route to the file.
        $datasetpath = $this->get_file_path($dataset);

        $cmd = 'python -m moodlemlbackend.training ' .
            escapeshellarg($uniqueid) . ' ' .
            escapeshellarg($outputdir) . ' ' .
            escapeshellarg($datasetpath);

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            debugging($cmd, DEBUG_DEVELOPER);
        }

        $output = null;
        $exitcode = null;
        $result = exec($cmd, $output, $exitcode);

        if (!$result) {
            throw new \moodle_exception('errornopredictresults', 'analytics');
        }

        if (!$resultobj = json_decode($result)) {
            throw new \moodle_exception('errorpredictwrongformat', 'analytics', '', json_last_error_msg());
        }

        if ($exitcode != 0) {
            throw new \moodle_exception('errorpredictionsprocessor', 'analytics', '', implode(', ', $resultobj->errors));
        }

        return $resultobj;
    }

    /**
     * Returns predictions for the provided dataset samples.
     *
     * @param string $uniqueid
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function predict($uniqueid, \stored_file $dataset, $outputdir) {

        // Obtain the physical route to the file.
        $datasetpath = $this->get_file_path($dataset);

        $cmd = 'python -m moodlemlbackend.prediction ' .
            escapeshellarg($uniqueid) . ' ' .
            escapeshellarg($outputdir) . ' ' .
            escapeshellarg($datasetpath);

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            debugging($cmd, DEBUG_DEVELOPER);
        }

        $output = null;
        $exitcode = null;
        $result = exec($cmd, $output, $exitcode);

        if (!$result) {
            throw new \moodle_exception('errornopredictresults', 'analytics');
        }

        if (!$resultobj = json_decode($result)) {
            throw new \moodle_exception('errorpredictwrongformat', 'analytics', '', json_last_error_msg());
        }

        if ($exitcode != 0) {
            throw new \moodle_exception('errorpredictionsprocessor', 'analytics', '', implode(', ', $resultobj->errors));
        }

        return $resultobj;
    }

    /**
     * Evaluates the provided dataset.
     *
     * @param string $uniqueid
     * @param float $maxdeviation
     * @param int $niterations
     * @param \stored_file $dataset
     * @param string $outputdir
     * @return \stdClass
     */
    public function evaluate($uniqueid, $maxdeviation, $niterations, \stored_file $dataset, $outputdir) {

        // Obtain the physical route to the file.
        $datasetpath = $this->get_file_path($dataset);

        $cmd = 'python -m moodlemlbackend.evaluation ' .
            escapeshellarg($uniqueid) . ' ' .
            escapeshellarg($outputdir) . ' ' .
            escapeshellarg($datasetpath) . ' ' .
            escapeshellarg(\core_analytics\model::MIN_SCORE) . ' ' .
            escapeshellarg($maxdeviation) . ' ' .
            escapeshellarg($niterations);

        if (!PHPUNIT_TEST && CLI_SCRIPT) {
            debugging($cmd, DEBUG_DEVELOPER);
        }

        $output = null;
        $exitcode = null;
        $result = exec($cmd, $output, $exitcode);

        if (!$result) {
            throw new \moodle_exception('errornopredictresults', 'analytics');
        }

        if (!$resultobj = json_decode($result)) {
            throw new \moodle_exception('errorpredictwrongformat', 'analytics', '', json_last_error_msg());
        }

        return $resultobj;
    }

    /**
     * Returns the path to the dataset file.
     *
     * @param \stored_file $file
     * @return string
     */
    protected function get_file_path(\stored_file $file) {
        // From moodle filesystem to the local file system.
        // This is not ideal, but there is no read access to moodle filesystem files.
        return $file->copy_content_to_temp('core_analytics');
    }
}
