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
 * Class for manipulating with the template records.
 *
 * @package    quizaccess_seb
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb;

use core\notification;
use quizaccess_seb\local\table\template_list;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for manipulating with the template records.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_controller {
    /**
     * View action.
     */
    const ACTION_VIEW = 'view';

    /**
     * Add action.
     */
    const ACTION_ADD = 'add';

    /**
     * Edit action.
     */
    const ACTION_EDIT = 'edit';

    /**
     * Delete action.
     */
    const ACTION_DELETE = 'delete';

    /**
     * Hide action.
     */
    const ACTION_HIDE = 'hide';

    /**
     * Show action.
     */
    const ACTION_SHOW = 'show';


    /**
     * Locally cached $OUTPUT object.
     * @var \bootstrap_renderer
     */
    protected $output;

    /**
     * region_manager constructor.
     */
    public function __construct() {
        global $OUTPUT;

        $this->output = $OUTPUT;
    }

    /**
     * Execute required action.
     *
     * @param string $action Action to execute.
     */
    public function execute($action) {

        $this->set_external_page();

        switch($action) {
            case self::ACTION_ADD:
            case self::ACTION_EDIT:
                $this->edit($action, optional_param('id', null, PARAM_INT));
                break;

            case self::ACTION_DELETE:
                $this->delete(required_param('id', PARAM_INT));
                break;

            case self::ACTION_HIDE:
                $this->hide(required_param('id', PARAM_INT));
                break;

            case self::ACTION_SHOW:
                $this->show(required_param('id', PARAM_INT));
                break;

            case self::ACTION_VIEW:
            default:
                $this->view();
                break;
        }
    }

    /**
     * Set external page for the manager.
     */
    protected function set_external_page() {
        admin_externalpage_setup('quizaccess_seb/template');
    }

    /**
     * Return record instance.
     *
     * @param int $id
     * @param \stdClass|null $data
     *
     * @return \quizaccess_seb\template
     */
    protected function get_instance($id = 0, \stdClass $data = null) {
        return new template($id, $data);
    }

    /**
     * Print out all records in a table.
     */
    protected function display_all_records() {
        $records = template::get_records([], 'id');

        $table = new template_list();
        $table->display($records);
    }

    /**
     * Returns a text for create new record button.
     * @return string
     */
    protected function get_create_button_text() : string {
        return get_string('addtemplate', 'quizaccess_seb');
    }

    /**
     * Returns form for the record.
     *
     * @param \quizaccess_seb\template|null $instance
     *
     * @return \quizaccess_seb\local\form\template
     */
    protected function get_form($instance) : \quizaccess_seb\local\form\template {
        global $PAGE;

        return new \quizaccess_seb\local\form\template($PAGE->url->out(false), ['persistent' => $instance]);
    }

    /**
     * View page heading string.
     * @return string
     */
    protected function get_view_heading() : string {
        return get_string('managetemplates', 'quizaccess_seb');
    }

    /**
     * New record heading string.
     * @return string
     */
    protected function get_new_heading() : string {
        return get_string('newtemplate', 'quizaccess_seb');
    }

    /**
     * Edit record heading string.
     * @return string
     */
    protected function get_edit_heading() : string {
        return get_string('edittemplate', 'quizaccess_seb');
    }

    /**
     * Returns base URL for the manager.
     * @return string
     */
    public static function get_base_url() : string {
        return '/mod/quiz/accessrule/seb/template.php';
    }

    /**
     * Execute edit action.
     *
     * @param string $action Could be edit or create.
     * @param null|int $id Id of the region or null if creating a new one.
     */
    protected function edit($action, $id = null) {
        global $PAGE;

        $PAGE->set_url(new \moodle_url(static::get_base_url(), ['action' => $action, 'id' => $id]));
        $instance = null;

        if ($id) {
            $instance = $this->get_instance($id);
        }

        $form = $this->get_form($instance);

        if ($form->is_cancelled()) {
            redirect(new \moodle_url(static::get_base_url()));
        } else if ($data = $form->get_data()) {
            unset($data->submitbutton);
            try {
                if (empty($data->id)) {
                    $data->content = $form->get_file_content('content');
                    $persistent = $this->get_instance(0, $data);
                    $persistent->create();

                    \quizaccess_seb\event\template_created::create_strict(
                        $persistent,
                        \context_system::instance()
                    )->trigger();
                    $this->trigger_enabled_event($persistent);
                } else {
                    $instance->from_record($data);
                    $instance->update();

                    \quizaccess_seb\event\template_updated::create_strict(
                        $instance,
                        \context_system::instance()
                    )->trigger();
                    $this->trigger_enabled_event($instance);
                }
                notification::success(get_string('changessaved'));
            } catch (\Exception $e) {
                notification::error($e->getMessage());
            }
            redirect(new \moodle_url(static::get_base_url()));
        } else {
            if (empty($instance)) {
                $this->header($this->get_new_heading());
            } else {
                if (!$instance->can_delete()) {
                    notification::warning(get_string('cantedit', 'quizaccess_seb'));
                }
                $this->header($this->get_edit_heading());
            }
        }

        $form->display();
        $this->footer();
    }

    /**
     * Execute delete action.
     *
     * @param int $id ID of the region.
     */
    protected function delete($id) {
        require_sesskey();
        $instance = $this->get_instance($id);

        if ($instance->can_delete()) {
            $instance->delete();
            notification::success(get_string('deleted'));

            \quizaccess_seb\event\template_deleted::create_strict(
                $id,
                \context_system::instance()
            )->trigger();

            redirect(new \moodle_url(static::get_base_url()));
        } else {
            notification::warning(get_string('cantdelete', 'quizaccess_seb'));
            redirect(new \moodle_url(static::get_base_url()));
        }
    }

    /**
     * Execute view action.
     */
    protected function view() {
        global $PAGE;

        $this->header($this->get_view_heading());
        $this->print_add_button();
        $this->display_all_records();

        // JS for Template management.
        $PAGE->requires->js_call_amd('quizaccess_seb/managetemplates', 'setup');

        $this->footer();
    }

    /**
     * Show the template.
     *
     * @param int $id The ID of the template to show.
     */
    protected function show(int $id) {
        $this->show_hide($id, 1);
    }

    /**
     * Hide the template.
     *
     * @param int $id The ID of the template to hide.
     */
    protected function hide($id) {
        $this->show_hide($id, 0);
    }

    /**
     * Show or Hide the template.
     *
     * @param int $id The ID of the template to hide.
     * @param int $visibility The intended visibility.
     */
    protected function show_hide(int $id, int $visibility) {
        require_sesskey();
        $template = $this->get_instance($id);
        $template->set('enabled', $visibility);
        $template->save();

        $this->trigger_enabled_event($template);

        redirect(new \moodle_url(self::get_base_url()));
    }

    /**
     * Print out add button.
     */
    protected function print_add_button() {
        echo $this->output->single_button(
            new \moodle_url(static::get_base_url(), ['action' => self::ACTION_ADD]),
            $this->get_create_button_text()
        );
    }

    /**
     * Print out page header.
     * @param string $title Title to display.
     */
    protected function header($title) {
        echo $this->output->header();
        echo $this->output->heading($title);
    }

    /**
     * Print out the page footer.
     *
     * @return void
     */
    protected function footer() {
        echo $this->output->footer();
    }

    /**
     * Helper function to fire off an event that informs of if a template is enabled or not.
     *
     * @param template $template The template persistent object.
     */
    private function trigger_enabled_event(template $template) {
        $eventstring = ($template->get('enabled') == 0 ? 'disabled' : 'enabled');

        $func = '\quizaccess_seb\event\template_' . $eventstring;
        $func::create_strict(
            $template,
            \context_system::instance()
        )->trigger();
    }

}