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
 * Contains class mod_feedback_templates_table
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\output\notification;
use core\output\action_menu;
use core\output\action_link;
use core\output\action_menu\link_secondary;
use core\output\actions\confirm_action;

global $CFG;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Class mod_feedback_templates_table
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_templates_table extends core_table\flexible_table {
    /** @var int|null The module id. */
    private $cmid;

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     * @param moodle_url $baseurl
     * @param string|null $mode This parameter has been deprecated since 4.5 and should not be used anymore.
     */
    public function __construct($uniqueid, $baseurl, ?string $mode = null) {
        if ($mode !== null) {
            debugging(
                'The age argument has been deprecated. Please remove it from your method calls.',
                DEBUG_DEVELOPER,
            );
        }
        parent::__construct($uniqueid);
        $this->cmid = $baseurl->param('id');
        $tablecolumns = [
            'template' => get_string('template', 'feedback'),
            'actions' => '',
        ];

        $this->set_attribute('class', 'templateslist');

        $this->define_columns(array_keys($tablecolumns));
        $this->define_headers(array_values($tablecolumns));
        $this->define_baseurl($baseurl);
        $this->column_class('template', 'template');
        $this->sortable(false);
    }

    /**
     * Displays the table with the given set of templates
     * @param array $templates
     */
    public function display($templates) {
        global $OUTPUT;
        if (empty($templates)) {
            echo $OUTPUT->notification(
                get_string('no_templates_available_yet', 'feedback'),
                notification::NOTIFY_INFO,
                false,
            );
            return;
        }

        $this->setup();

        foreach ($templates as $template) {
            $showactions = has_any_capability(
                ['mod/feedback:deletetemplate', 'mod/feedback:edititems', 'mod/feedback:createpublictemplate'],
                $this->get_context()
            );
            $data = [
                format_string($template->name),
                $showactions ? $OUTPUT->render($this->get_row_actions($template)) : '',
            ];

            $this->add_data($data);
        }
        $this->finish_output();
    }

    /**
     * Get the row actions for the given template
     *
     * @param stdClass $template
     * @return action_menu
     */
    private function get_row_actions(stdClass $template): action_menu {
        global $PAGE, $OUTPUT;

        $url = new moodle_url($this->baseurl, ['templateid' => $template->id, 'sesskey' => sesskey()]);
        $strdeletefeedback = get_string('delete_template', 'feedback');
        $actions = new action_menu();
        $actions->set_menu_trigger($OUTPUT->pix_icon('a/setting', get_string('actions')));

        // Preview.
        $actions->add(new link_secondary(
            new moodle_url($this->baseurl, ['templateid' => $template->id, 'sesskey' => sesskey()]),
            new pix_icon('t/preview', get_string('preview')),
            get_string('preview'),
        ));

        // Use template.
        if (has_capability('mod/feedback:edititems', context_module::instance($this->cmid))) {
            $PAGE->requires->js_call_amd('mod_feedback/usetemplate', 'init');
            $actions->add(new link_secondary(
                new moodle_url('#'),
                new pix_icon('i/files', get_string('preview')),
                get_string('use_this_template', 'mod_feedback'),
                ['data-action' => 'usetemplate', 'data-dataid' => $this->cmid, 'data-templateid' => $template->id],
            ));
        }

        // Delete.
        $showdelete = has_capability('mod/feedback:deletetemplate', context_module::instance($this->cmid));
        if ($template->ispublic) {
            $showdelete = has_all_capabilities(
                ['mod/feedback:createpublictemplate', 'mod/feedback:deletetemplate'],
                context_system::instance()
            );
        }
        if ($showdelete) {
            $exporturl = new moodle_url(
                '/mod/feedback/manage_templates.php',
                $url->params() + ['deletetemplate' => $template->id]
            );
            $deleteaction = new action_link(
                $exporturl,
                get_string('delete'),
                new confirm_action(get_string('confirmdeletetemplate', 'feedback')),
                ['class' => 'text-danger'],
                new pix_icon('t/delete', $strdeletefeedback),
            );
            $actions->add_secondary_action($deleteaction);
        }

        return $actions;
    }
}
