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
 * Model configuration manager.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Model configuration manager.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class model_config {

    /**
     * @var \core_analytics\model
     */
    private $model = null;

    /**
     * The name of the file where config is held.
     */
    const CONFIG_FILE_NAME = 'model-config.json';

    /**
     * Constructor.
     *
     * @param \core_analytics\model|null $model
     */
    public function __construct(?model $model = null) {
        $this->model = $model;
    }

    /**
     * Exports a model to a zip using the provided file name.
     *
     * @param string $zipfilename
     * @param bool $includeweights Include the model weights if available
     * @return string
     */
    public function export(string $zipfilename, bool $includeweights = true) : string {

        if (!$this->model) {
            throw new \coding_exception('No model object provided.');
        }

        if (!$this->model->can_export_configuration()) {
            throw new \moodle_exception('errornoexportconfigrequirements', 'analytics');
        }

        $zip = new \zip_packer();
        $zipfiles = [];

        // Model config in JSON.
        $modeldata = $this->export_model_data();

        $exporttmpdir = make_request_directory();
        $jsonfilepath = $exporttmpdir . DIRECTORY_SEPARATOR . 'model-config.json';
        if (!file_put_contents($jsonfilepath, json_encode($modeldata))) {
            print_error('errornoexportconfig', 'analytics');
        }
        $zipfiles[self::CONFIG_FILE_NAME] = $jsonfilepath;

        // ML backend.
        if ($includeweights && $this->model->is_trained()) {
            $processor = $this->model->get_predictions_processor(true);
            $outputdir = $this->model->get_output_dir(array('execution'));
            $mlbackenddir = $processor->export($this->model->get_unique_id(), $outputdir);
            $mlbackendfiles = get_directory_list($mlbackenddir);
            foreach ($mlbackendfiles as $mlbackendfile) {
                $fullpath = $mlbackenddir . DIRECTORY_SEPARATOR . $mlbackendfile;
                // Place the ML backend files inside a mlbackend/ dir.
                $zipfiles['mlbackend/' . $mlbackendfile] = $fullpath;
            }
        }

        $zipfilepath = $exporttmpdir . DIRECTORY_SEPARATOR . $zipfilename;
        $zip->archive_to_pathname($zipfiles, $zipfilepath);

        return $zipfilepath;
    }

    /**
     * Imports the provided model configuration into a new model.
     *
     * Note that this method assumes that self::check_dependencies has already been called.
     *
     * @param  string $zipfilepath Path to the zip file to import
     * @return \core_analytics\model
     */
    public function import(string $zipfilepath) : \core_analytics\model {

        list($modeldata, $mlbackenddir) = $this->extract_import_contents($zipfilepath);

        $target = \core_analytics\manager::get_target($modeldata->target);
        $indicators = [];
        foreach ($modeldata->indicators as $indicatorclass) {
            $indicator = \core_analytics\manager::get_indicator($indicatorclass);
            $indicators[$indicator->get_id()] = $indicator;
        }
        $model = \core_analytics\model::create($target, $indicators, $modeldata->timesplitting, $modeldata->processor);

        // Import them disabled.
        $model->update(false, false, false, false);

        if ($mlbackenddir) {
            $modeldir = $model->get_output_dir(['execution']);
            if (!$model->get_predictions_processor(true)->import($model->get_unique_id(), $modeldir, $mlbackenddir)) {
                throw new \moodle_exception('errorimport', 'analytics');
            }
            $model->mark_as_trained();
        }

        return $model;
    }

    /**
     * Check that the provided model configuration can be deployed in this site.
     *
     * @param  \stdClass $modeldata
     * @param  bool $ignoreversionmismatches
     * @return string|null Error string or null if all good.
     */
    public function check_dependencies(\stdClass $modeldata, bool $ignoreversionmismatches) : ?string {

        $siteversions = \core_component::get_all_versions();

        // Possible issues.
        $missingcomponents = [];
        $versionmismatches = [];
        $missingclasses = [];

        // We first check that this site has the required dependencies and the required versions.
        foreach ($modeldata->dependencies as $component => $importversion) {

            if (empty($siteversions[$component])) {

                if ($component === 'core') {
                    $component = 'Moodle';
                }
                $missingcomponents[$component] = $component . ' (' . $importversion . ')';
                continue;
            }

            if ($siteversions[$component] == $importversion) {
                // All good here.
                continue;
            }

            if (!$ignoreversionmismatches) {
                if ($component === 'core') {
                    $component = 'Moodle';
                }
                $versionmismatches[$component] = $component . ' (' . $importversion . ')';
            }
        }

        // Checking that each of the components is available.
        if (!$target = manager::get_target($modeldata->target)) {
            $missingclasses[] = $modeldata->target;
        }

        if (!$timesplitting = manager::get_time_splitting($modeldata->timesplitting)) {
            $missingclasses[] = $modeldata->timesplitting;
        }

        // Indicators.
        foreach ($modeldata->indicators as $indicatorclass) {
            if (!$indicator = manager::get_indicator($indicatorclass)) {
                $missingclasses[] = $indicatorclass;
            }
        }

        // ML backend.
        if (!empty($modeldata->processor)) {
            if (!$processor = \core_analytics\manager::get_predictions_processor($modeldata->processor, false)) {
                $missingclasses[] = $indicatorclass;
            }
        }

        if (!empty($missingcomponents)) {
            return get_string('errorimportmissingcomponents', 'analytics', join(', ', $missingcomponents));
        }

        if (!empty($versionmismatches)) {
            return get_string('errorimportversionmismatches', 'analytics', implode(', ', $versionmismatches));
        }

        if (!empty($missingclasses)) {
            $a = (object)[
                'missingclasses' => implode(', ', $missingclasses),
            ];
            return get_string('errorimportmissingclasses', 'analytics', $a);
        }

        // No issues found.
        return null;
    }

    /**
     * Returns the component the class belongs to.
     *
     * Note that this method does not work for global space classes.
     *
     * @param  string $fullclassname Qualified name including the namespace.
     * @return string|null Frankenstyle component
     */
    public static function get_class_component(string $fullclassname) : ?string {

        // Strip out leading backslash.
        $fullclassname = ltrim($fullclassname, '\\');

        $nextbackslash = strpos($fullclassname, '\\');
        if ($nextbackslash === false) {
            // Global space.
            return 'core';
        }
        $component = substr($fullclassname, 0, $nextbackslash);

        // All core subsystems use core's version.php.
        if (strpos($component, 'core_') === 0) {
            $component = 'core';
        }

        return $component;
    }

    /**
     * Extracts the import zip contents.
     *
     * @param  string $zipfilepath Zip file path
     * @return array [0] => \stdClass, [1] => string
     */
    public function extract_import_contents(string $zipfilepath) : array {

        $importtempdir = make_request_directory();

        $zip = new \zip_packer();
        $filelist = $zip->extract_to_pathname($zipfilepath, $importtempdir);

        if (empty($filelist[self::CONFIG_FILE_NAME])) {
            // Missing required file.
            throw new \moodle_exception('errorimport', 'analytics');
        }

        $jsonmodeldata = file_get_contents($importtempdir . DIRECTORY_SEPARATOR . self::CONFIG_FILE_NAME);

        if (!$modeldata = json_decode($jsonmodeldata)) {
            throw new \moodle_exception('errorimport', 'analytics');
        }

        if (empty($modeldata->target) || empty($modeldata->timesplitting) || empty($modeldata->indicators)) {
            throw new \moodle_exception('errorimport', 'analytics');
        }

        $mlbackenddir = $importtempdir . DIRECTORY_SEPARATOR . 'mlbackend';
        if (!is_dir($mlbackenddir)) {
            $mlbackenddir = false;
        }

        return [$modeldata, $mlbackenddir];
    }
    /**
     * Exports the configuration of the model.
     * @return \stdClass
     */
    protected function export_model_data() : \stdClass {

        $versions = \core_component::get_all_versions();

        $data = new \stdClass();

        // Target.
        $data->target = $this->model->get_target()->get_id();
        $requiredclasses[] = $data->target;

        // Time splitting method.
        $data->timesplitting = $this->model->get_time_splitting()->get_id();
        $requiredclasses[] = $data->timesplitting;

        // Model indicators.
        $data->indicators = [];
        foreach ($this->model->get_indicators() as $indicator) {
            $indicatorid = $indicator->get_id();
            $data->indicators[] = $indicatorid;
            $requiredclasses[] = $indicatorid;
        }

        // Return the predictions processor this model is using, even if no predictions processor
        // was explicitly selected.
        $predictionsprocessor = $this->model->get_predictions_processor();
        $data->processor = '\\' . get_class($predictionsprocessor);
        $requiredclasses[] = $data->processor;

        // Add information for versioning.
        $data->dependencies = [];
        foreach ($requiredclasses as $fullclassname) {
            $component = $this->get_class_component($fullclassname);
            $data->dependencies[$component] = $versions[$component];
        }

        return $data;
    }
}
