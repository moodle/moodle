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
 * Class for exporting competency_path data.
 *
 * @package    tool_lp
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;

/**
 * Class for exporting competency_path data.
 *
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_path_exporter extends \core\external\exporter {

    /**
     * Constructor.
     *
     * @param array $related - related objects.
     */
    public function __construct($related) {
        parent::__construct([], $related);
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'ancestors' => 'core_competency\\competency[]',
            'framework' => 'core_competency\\competency_framework',
            'context' => 'context'
        ];
    }

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array - Keys with their types.
     */
    protected static function define_other_properties() {
        return [
            'ancestors' => [
                'type' => path_node_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'framework' => [
                'type' => path_node_exporter::read_properties_definition()
            ],
            'pluginbaseurl' => [
                'type' => PARAM_URL
            ],
            'pagecontextid' => [
                'type' => PARAM_INT
            ],
            'showlinks' => [
                'type' => PARAM_BOOL
            ]
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $result = new \stdClass();
        $ancestors = [];
        $nodescount = count($this->related['ancestors']);
        $i = 1;
        $result->showlinks = \core_competency\api::show_links();
        foreach ($this->related['ancestors'] as $competency) {
            $exporter = new path_node_exporter([
                    'id' => $competency->get('id'),
                    'name' => $competency->get('idnumber'),
                    'position' => $i,
                    'first' => $i == 1,
                    'last' => $i == $nodescount
                ], [
                    'context' => $this->related['context'],
                ]
            );
            $ancestors[] = $exporter->export($output);
            $i++;
        }
        $result->ancestors = $ancestors;
        $exporter = new path_node_exporter([
                'id' => $this->related['framework']->get('id'),
                'name' => $this->related['framework']->get('shortname'),
                'first' => 0,
                'last' => 0,
                'position' => -1
            ], [
                'context' => $this->related['context']
            ]
        );
        $result->framework = $exporter->export($output);
        $result->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(true);
        $result->pagecontextid = $this->related['context']->id;
        return (array) $result;
    }
}
