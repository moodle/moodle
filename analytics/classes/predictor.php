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
 * Predictions processor interface.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Predictors interface.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface predictor {

    /**
     * Is it ready to predict?
     *
     * @return bool
     */
    public function is_ready();

    /**
     * Delete all stored information of the current model id.
     *
     * This method is called when there are important changes to a model,
     * all previous training algorithms using that version of the model
     * should be deleted.
     *
     * In case you want to perform extra security measures before deleting
     * a directory you can check that $modelversionoutputdir subdirectories
     * can only be named 'execution', 'evaluation' or 'testing'.
     *
     * @param string $uniqueid The site model unique id string
     * @param string $modelversionoutputdir The output dir of this model version
     * @return null
     */
    public function clear_model($uniqueid, $modelversionoutputdir);

    /**
     * Delete the output directory.
     *
     * This method is called when a model is completely deleted.
     *
     * In case you want to perform extra security measures before deleting
     * a directory you can check that the subdirectories are timestamps
     * (the model version) and each of this subdirectories' subdirectories
     * can only be named 'execution', 'evaluation' or 'testing'.
     *
     * @param string $modeloutputdir The model directory id (parent of all model versions subdirectories).
     * @return null
     */
    public function delete_output_dir($modeloutputdir);

}
