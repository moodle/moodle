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

use core_completion\manager;
use core_course\output\activity_icon;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/renderer.php');

/**
 * Main renderer for the bulk activity completion stuff.
 *
 * @package core_course
 * @copyright 2017 Adrian Greeve
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_bulk_activity_completion_renderer extends plugin_renderer_base {

    /**
     * Render the bulk completion tab.
     *
     * @param array|stdClass $data the context data to pass to the template.
     * @return bool|string
     */
    public function bulkcompletion($data) {
        return parent::render_from_template('core_course/bulkactivitycompletion', $data);
    }

    /**
     * Render the default completion tab.
     *
     * @param array|stdClass $data the context data to pass to the template.
     * @param array $modules The modules that have been sent through the form.
     * @param moodleform $form The current form that has been sent.
     * @return bool|string
     */
    public function defaultcompletion($data, $modules, $form) {
        $course = get_course($data->courseid);
        foreach ($data->modules as $module) {
            // If the user can manage this module, then the activity completion form needs to be returned too, without the
            // cancel button (so only "Save changes" button is displayed).
            if ($module->canmanage) {
                // Only create the form if it's different from the one that has been sent.
                $modform = $form;
                $module->open = true;
                if (empty($form) || !in_array($module->id, array_keys($modules))) {
                    $modform = new \core_completion_defaultedit_form(
                        null,
                        [
                            'course' => $course,
                            'modules' => [
                                $module->id => $module,
                            ],
                            'displaycancel' => false,
                            'forceuniqueid' => true,
                        ],
                    );
                    $module->open = false;
                }

                $module->activityicon = activity_icon::from_modname($module->name)
                    ->set_extra_classes('smaller')
                    ->export_for_template($this);

                $moduleform = manager::get_module_form($module->name, $course);
                if ($moduleform) {
                    $module->formhtml = $modform->render();
                } else {
                    // If the module form is not available, then display a message.
                    $module->formhtml = $this->output->notification(
                        get_string('incompatibleplugin', 'completion'),
                        \core\output\notification::NOTIFY_INFO,
                        false
                    );
                }
            }
        }
        $data->issite = $course->id == SITEID;

        return parent::render_from_template('core_course/defaultactivitycompletion', $data);
    }

    /**
     * Renders the form for bulk editing activities completion
     *
     * @param moodleform $form
     * @param array $activities
     * @return string
     */
    public function edit_bulk_completion($form, $activities) {
        ob_start();
        $form->display();
        $formhtml = ob_get_contents();
        ob_end_clean();

        $data = (object)[
            'form' => $formhtml,
            'activities' => array_values($activities),
            'activitiescount' => count($activities),
        ];
        return parent::render_from_template('core_course/editbulkactivitycompletion', $data);
    }

    /**
     * @deprecated since Moodle 4.3 MDL-78528
     */
    #[\core\attribute\deprecated(null, since: '4.3', mdl: 'MDL-78528', final: true)]
    public function edit_default_completion() {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Renders the course completion action bar.
     *
     * @param \core_course\output\completion_action_bar $actionbar
     * @return string The HTML output
     */
    public function render_course_completion_action_bar(\core_course\output\completion_action_bar $actionbar): string {
        $data = $actionbar->export_for_template($this->output);
        return $this->output->render_from_template('core_course/completion_action_bar', $data);
    }
}
