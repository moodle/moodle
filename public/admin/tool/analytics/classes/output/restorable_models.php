<?php
// This file is part of Moodle - https://moodle.org/
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
 * Provides {@link \tool_analytics\output\restorable_models} class.
 *
 * @package     tool_analytics
 * @category    output
 * @copyright   2019 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents the list of default models that can be eventually restored.
 *
 * @copyright 2019 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restorable_models implements \renderable, \templatable {

    /** @var array */
    protected $models;

    /**
     * Instantiate an object of this class.
     *
     * @param array $models List of models as returned by {@link \core_analytics\manager::load_default_models_for_all_components()}
     */
    public function __construct(array $models) {

        $this->models = $models;
    }

    /**
     * Export the list of models to be rendered.
     *
     * @param \renderer_base $output
     * @return string
     */
    public function export_for_template(\renderer_base $output) {

        $components = [];

        foreach ($this->models as $componentname => $modelslist) {
            $component = [
                'name' => $this->component_name($componentname),
                'component' => $componentname,
                'models' => [],
            ];

            foreach ($modelslist as $definition) {
                list($target, $indicators) = \core_analytics\manager::get_declared_target_and_indicators_instances($definition);

                if (\core_analytics\model::exists($target, $indicators)) {
                    continue;
                }

                $targetnamelangstring = $target->get_name();

                $model = [
                    'defid' => \core_analytics\manager::model_declaration_identifier($definition),
                    'targetname' => $targetnamelangstring,
                    'targetclass' => $definition['target'],
                    'indicatorsnum' => count($definition['indicators']),
                    'indicators' => [],
                ];

                if (get_string_manager()->string_exists($targetnamelangstring->get_identifier().'_help',
                        $targetnamelangstring->get_component())) {
                    $helpicon = new \help_icon($targetnamelangstring->get_identifier(), $targetnamelangstring->get_component());
                    $model['targethelp'] = $helpicon->export_for_template($output);
                }

                foreach ($indicators as $indicator) {
                    $indicatornamelangstring = $indicator->get_name();
                    $indicatordata = [
                        'name' => $indicatornamelangstring,
                        'classname' => $indicator->get_id(),
                    ];

                    if (get_string_manager()->string_exists($indicatornamelangstring->get_identifier().'_help',
                            $indicatornamelangstring->get_component())) {
                        $helpicon = new \help_icon($indicatornamelangstring->get_identifier(),
                            $indicatornamelangstring->get_component());
                        $indicatordata['indicatorhelp'] = $helpicon->export_for_template($output);
                    }

                    $model['indicators'][] = $indicatordata;
                }

                $component['models'][] = $model;
            }

            if (!empty($component['models'])) {
                $components[] = $component;
            }
        }

        $result = [
            'hasdata' => !empty($components),
            'components' => array_values($components),
            'submiturl' => new \moodle_url('/admin/tool/analytics/restoredefault.php'),
            'backurl' => new \moodle_url('/admin/tool/analytics/index.php'),
            'sesskey' => sesskey(),
        ];

        return $result;
    }

    /**
     * Return a human readable name for the given frankenstyle component.
     *
     * @param string $component Frankenstyle component such as 'core', 'core_analytics' or 'mod_workshop'
     * @return string Human readable name of the component
     */
    protected function component_name(string $component): string {

        if ($component === 'core' || strpos($component, 'core_')) {
            return get_string('componentcore', 'tool_analytics');

        } else {
            return get_string('pluginname', $component);
        }
    }
}
