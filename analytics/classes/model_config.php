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
     * Constructor.
     *
     * @param \core_analytics\model|null $model
     */
    public function __construct(?model $model = null) {
        $this->model = $model;
    }

    /**
     * Exports a model to a temp file using the provided file name.
     *
     * @return \stdClass
     */
    public function export() : \stdClass {

        if (!$this->model) {
            throw new \coding_exception('No model object provided.');
        }

        if (!$this->model->can_export_configuration()) {
            throw new \moodle_exception('errornoexportconfigrequirements', 'tool_analytics');
        }

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

        if ($processor = $this->model->get_model_obj()->predictionsprocessor) {
            $data->processor = $processor;
        }
        // Add information for versioning.
        $data->dependencies = [];
        foreach ($requiredclasses as $fullclassname) {
            $component = $this->get_class_component($fullclassname);
            $data->dependencies[$component] = $versions[$component];
        }

        return $data;
    }

    /**
     * Packages the configuration of a model into a .json file.
     *
     * @param  \stdClass $data Model config data
     * @param  string $downloadfilename The file name.
     * @return string Path to the file with the model configuration.
     */
    public function export_to_file(\stdClass $data, string $downloadfilename) : string {

        $modelconfig = json_encode($data);

        $dir = make_temp_directory('analyticsexport');
        $filepath = $dir . DIRECTORY_SEPARATOR . $downloadfilename;
        if (!file_put_contents($filepath, $modelconfig)) {
            print_error('errornoexportconfig', 'tool_analytics');
        }

        return $filepath;
    }

    /**
     * Check the provided json string.
     *
     * @param  string $json A json string.
     * @return string|null Error string or null if all good.
     */
    public function check_json_data(string $json) : ?string {

        if (!$modeldata = json_decode($json)) {
            return get_string('errorimport', 'tool_analytics');
        }

        if (empty($modeldata->target) || empty($modeldata->timesplitting) || empty($modeldata->indicators)) {
            return get_string('errorimport', 'tool_analytics');
        }

        return null;
    }

    /**
     * Check that the provided model configuration can be deployed in this site.
     *
     * @param  \stdClass $importmodel
     * @param  bool $ignoreversionmismatches
     * @return string|null Error string or null if all good.
     */
    public function check_dependencies(\stdClass $importmodel, bool $ignoreversionmismatches) : ?string {

        $siteversions = \core_component::get_all_versions();

        // Possible issues.
        $missingcomponents = [];
        $versionmismatches = [];
        $missingclasses = [];

        // We first check that this site has the required dependencies and the required versions.
        foreach ($importmodel->dependencies as $component => $importversion) {

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

        // Checking that the each of the components is available.
        if (!$target = manager::get_target($importmodel->target)) {
            $missingclasses[] = $importmodel->target;
        }

        if (!$timesplitting = manager::get_time_splitting($importmodel->timesplitting)) {
            $missingclasses[] = $importmodel->timesplitting;
        }

        // Indicators.
        $indicators = [];
        foreach ($importmodel->indicators as $indicatorclass) {
            if (!$indicator = manager::get_indicator($indicatorclass)) {
                $missingclasses[] = $indicatorclass;
            }
        }

        // ML backend.
        if (!empty($importmodel->processor)) {
            if (!$processor = \core_analytics\manager::get_predictions_processor($importmodel->processor, false)) {
                $missingclasses[] = $indicatorclass;
            }
        }

        if (!empty($missingcomponents)) {
            return get_string('errorimportmissingcomponents', 'tool_analytics', join(', ', $missingcomponents));
        }

        if (!empty($versionmismatches)) {
            return get_string('errorimportversionmismatches', 'tool_analytics', implode(', ', $versionmismatches));
        }

        if (!empty($missingclasses)) {
            $a = (object)[
                'missingclasses' => implode(', ', $missingclasses),
                'dependencyversions' => implode(', ', $dependencyversions)
            ];
            return get_string('errorimportmissingclasses', 'tool_analytics', $a);
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
}
