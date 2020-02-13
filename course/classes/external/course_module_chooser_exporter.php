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
 * Author exporter.
 *
 * @package    core_course
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;

/**
 * Course module chooser exporter.
 *
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_module_chooser_exporter extends exporter {

    /** @var array $modules Array containing the available modules */
    private $modules;

    /**
     * Constructor.
     *
     * @param array $modules The available course modules
     * @param array $related The related data for the export
     */
    public function __construct(array $modules, array $related = []) {
        $this->modules = $modules;
        return parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'options' => [
                'multiple' => true,
                'optional' => true,
                'type' => [
                    'label' => ['type' => PARAM_TEXT],
                    'modulename' => ['type' => PARAM_TEXT],
                    'description' => ['type' => PARAM_TEXT],
                    'urls' => [
                        'type' => [
                            'addoption' => [
                                'type' => PARAM_URL
                            ]
                        ]
                    ],
                    'icon' => [
                        'type' => PARAM_RAW,
                        'optional' => true,
                        'default' => null,
                        'null' => NULL_ALLOWED
                    ]
                ]
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

        $options = new \stdClass();
        $options->trusted = false;
        $options->noclean = false;
        $options->smiley = false;
        $options->filter = false;
        $options->para = true;
        $options->newlines = false;
        $options->overflowdiv = false;

        $context = $this->related['context'];

        $modulesdata = [];
        foreach ($this->modules as $module) {
            $customiconurl = null;

            // The property 'name' may contain more than just the module, in which case we need to extract the true module name.
            $modulename = $module->name;
            if ($colon = strpos($modulename, ':')) {
                $modulename = substr($modulename, 0, $colon);
            }

            if (isset($module->help) || !empty($module->help)) {
                list($description) = external_format_text((string) $module->help, FORMAT_MARKDOWN,
                    $context->id, null, null, null, $options);
            } else {
                $description = get_string('nohelpforactivityorresource', 'moodle');
            }

            $icon = new \pix_icon('icon', '', $modulename);

            // When exporting check if the title is an object, we assume it's a lang string object otherwise we send the raw string.
            $modulesdata[] = [
                'label' => $module->title instanceof \lang_string ? $module->title->out() : $module->title,
                'modulename' => $modulename,
                'description' => $description,
                'urls' => [
                    'addoption' => $module->link->out(false),
                ],
                'icon' => $icon->export_for_template($output)
            ];
        }

        return [
            'options' => $modulesdata
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context'
        ];
    }
}
