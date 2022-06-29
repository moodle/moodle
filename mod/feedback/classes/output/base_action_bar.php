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

namespace mod_feedback\output;

use context_module;
use renderable;
use renderer_base;
use templatable;

/**
 * Class base_action_bar
 *
 * Base class to be inherited by any other feedback action bar
 *
 * @package     mod_feedback
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_action_bar implements renderable, templatable {
    /** @var int $cmid The module id */
    protected $cmid;
    /** @var object $context The context we are in */
    protected $context;
    /** @var object $course The course we are in */
    protected $course;
    /** @var array $urlparams The default params to be used when creating urls */
    protected $urlparams;
    /** @var object $feedback The activity record that is being viewed */
    protected $feedback;

    /**
     * base_action_bar constructor.
     *
     * @param int $cmid
     */
    public function __construct(int $cmid) {
        global $PAGE;
        $this->cmid = $cmid;
        $this->context = context_module::instance($cmid);
        [$course, $cm] = get_course_and_cm_from_cmid($cmid);
        $this->course = $course;
        $this->urlparams = [
            'id' => $cmid
        ];
        $this->feedback = $PAGE->activityrecord;
    }

    /**
     * Recursively iterates through to array of renderables and exports
     *
     * @param array $items Collection of renderables
     * @param renderer_base $output
     * @return array $items Data to be used in the mustache template
     */
    private function export_items_for_template(array $items, renderer_base $output): array {
        $items = array_map(function($item) use ($output) {
            if (is_array($item)) {
                return $this->export_items_for_template($item, $output);
            }

            if (is_object($item) && method_exists($item, 'export_for_template')) {
                return $item->export_for_template($output);
            }

            return $item;
        }, $items);

        return $items;
    }

    /**
     * Export the data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $items = $this->export_items_for_template($this->get_items(), $output);
        return $items;
    }

    /**
     * Function to generate a list of renderables to be displayed
     * @return array
     */
    abstract protected function get_items(): array;
}
