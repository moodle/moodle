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
 * Tour manager.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours;

defined('MOODLE_INTERNAL') || die();

use tool_usertours\local\forms;
use tool_usertours\local\table;
use core\notification;

/**
 * Tour manager.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * @var ACTION_LISTTOURS      The action to get the list of tours.
     */
    const ACTION_LISTTOURS = 'listtours';

    /**
     * @var ACTION_NEWTOUR        The action to create a new tour.
     */
    const ACTION_NEWTOUR = 'newtour';

    /**
     * @var ACTION_EDITTOUR       The action to edit the tour.
     */
    const ACTION_EDITTOUR = 'edittour';

    /**
     * @var ACTION_MOVETOUR The action to move a tour up or down.
     */
    const ACTION_MOVETOUR = 'movetour';

    /**
     * @var ACTION_EXPORTTOUR     The action to export the tour.
     */
    const ACTION_EXPORTTOUR = 'exporttour';

    /**
     * @var ACTION_IMPORTTOUR     The action to import the tour.
     */
    const ACTION_IMPORTTOUR = 'importtour';

    /**
     * @var ACTION_DELETETOUR     The action to delete the tour.
     */
    const ACTION_DELETETOUR = 'deletetour';

    /**
     * @var ACTION_VIEWTOUR       The action to view the tour.
     */
    const ACTION_VIEWTOUR = 'viewtour';

    /**
     * @var ACTION_DUPLICATETOUR     The action to duplicate the tour.
     */
    const ACTION_DUPLICATETOUR = 'duplicatetour';

    /**
     * @var ACTION_NEWSTEP The action to create a new step.
     */
    const ACTION_NEWSTEP = 'newstep';

    /**
     * @var ACTION_EDITSTEP The action to edit step configuration.
     */
    const ACTION_EDITSTEP = 'editstep';

    /**
     * @var ACTION_MOVESTEP The action to move a step up or down.
     */
    const ACTION_MOVESTEP = 'movestep';

    /**
     * @var ACTION_DELETESTEP The action to delete a step.
     */
    const ACTION_DELETESTEP = 'deletestep';

    /**
     * @var ACTION_VIEWSTEP The action to view a step.
     */
    const ACTION_VIEWSTEP = 'viewstep';

    /**
     * @var ACTION_HIDETOUR The action to hide a tour.
     */
    const ACTION_HIDETOUR = 'hidetour';

    /**
     * @var ACTION_SHOWTOUR The action to show a tour.
     */
    const ACTION_SHOWTOUR = 'showtour';

    /**
     * @var ACTION_RESETFORALL
     */
    const ACTION_RESETFORALL = 'resetforall';

    /**
     * @var CONFIG_SHIPPED_TOUR
     */
    const CONFIG_SHIPPED_TOUR = 'shipped_tour';

    /**
     * @var CONFIG_SHIPPED_FILENAME
     */
    const CONFIG_SHIPPED_FILENAME = 'shipped_filename';

    /**
     * @var CONFIG_SHIPPED_VERSION
     */
    const CONFIG_SHIPPED_VERSION = 'shipped_version';

    /**
     * Helper method to initialize admin page, setting appropriate extra URL parameters
     *
     * @param string $action
     */
    protected function setup_admin_externalpage(string $action): void {
        admin_externalpage_setup('tool_usertours/tours', '', array_filter([
            'action' => $action,
            'id' => optional_param('id', 0, PARAM_INT),
            'tourid' => optional_param('tourid', 0, PARAM_INT),
            'direction' => optional_param('direction', 0, PARAM_INT),
        ]));
    }

    /**
     * This is the entry point for this controller class.
     *
     * @param   string  $action     The action to perform.
     */
    public function execute($action) {
        $this->setup_admin_externalpage($action);

        // Add the main content.
        switch($action) {
            case self::ACTION_NEWTOUR:
            case self::ACTION_EDITTOUR:
                $this->edit_tour(optional_param('id', null, PARAM_INT));
                break;

            case self::ACTION_MOVETOUR:
                $this->move_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_EXPORTTOUR:
                $this->export_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_IMPORTTOUR:
                $this->import_tour();
                break;

            case self::ACTION_VIEWTOUR:
                $this->view_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_DUPLICATETOUR:
                $this->duplicate_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_HIDETOUR:
                $this->hide_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_SHOWTOUR:
                $this->show_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_DELETETOUR:
                $this->delete_tour(required_param('id', PARAM_INT));
                break;

            case self::ACTION_RESETFORALL:
                $this->reset_tour_for_all(required_param('id', PARAM_INT));
                break;

            case self::ACTION_NEWSTEP:
            case self::ACTION_EDITSTEP:
                $this->edit_step(optional_param('id', null, PARAM_INT));
                break;

            case self::ACTION_MOVESTEP:
                $this->move_step(required_param('id', PARAM_INT));
                break;

            case self::ACTION_DELETESTEP:
                $this->delete_step(required_param('id', PARAM_INT));
                break;

            case self::ACTION_LISTTOURS:
            default:
                $this->print_tour_list();
                break;
        }
    }

    /**
     * Print out the page header.
     *
     * @param   string  $title     The title to display.
     */
    protected function header($title = null) {
        global $OUTPUT;

        // Print the page heading.
        echo $OUTPUT->header();

        if ($title === null) {
            $title = get_string('tours', 'tool_usertours');
        }

        echo $OUTPUT->heading($title);
    }

    /**
     * Print out the page footer.
     *
     * @return void
     */
    protected function footer() {
        global $OUTPUT;

        echo $OUTPUT->footer();
    }

    /**
     * Print the the list of tours.
     */
    protected function print_tour_list() {
        global $PAGE, $OUTPUT;

        $this->header();
        echo \html_writer::span(get_string('tourlist_explanation', 'tool_usertours'));
        $table = new table\tour_list();
        $tours = helper::get_tours();
        foreach ($tours as $tour) {
            $table->add_data_keyed($table->format_row($tour));
        }

        $table->finish_output();
        $actions = [
            (object) [
                'link'  => helper::get_edit_tour_link(),
                'linkproperties' => [],
                'img'   => 'b/tour-new',
                'title' => get_string('newtour', 'tool_usertours'),
            ],
            (object) [
                'link'  => helper::get_import_tour_link(),
                'linkproperties' => [],
                'img'   => 'b/tour-import',
                'title' => get_string('importtour', 'tool_usertours'),
            ],
            (object) [
                'link'  => new \moodle_url('https://archive.moodle.net/tours'),
                'linkproperties' => [
                        'target' => '_blank',
                    ],
                'img'   => 'b/tour-shared',
                'title' => get_string('sharedtourslink', 'tool_usertours'),
            ],
        ];

        echo \html_writer::start_tag('div', [
                'class' => 'tour-actions',
            ]);

        echo \html_writer::start_tag('ul');
        foreach ($actions as $config) {
            $action = \html_writer::start_tag('li');
            $linkproperties = $config->linkproperties;
            $linkproperties['href'] = $config->link;
            $action .= \html_writer::start_tag('a', $linkproperties);
            $action .= $OUTPUT->pix_icon($config->img, $config->title, 'tool_usertours');
            $action .= \html_writer::div($config->title);
            $action .= \html_writer::end_tag('a');
            $action .= \html_writer::end_tag('li');
            echo $action;
        }
        echo \html_writer::end_tag('ul');
        echo \html_writer::end_tag('div');

        // JS for Tour management.
        $PAGE->requires->js_call_amd('tool_usertours/managetours', 'setup');
        $this->footer();
    }

    /**
     * Return the edit tour link.
     *
     * @param   int         $id     The ID of the tour
     * @return string
     */
    protected function get_edit_tour_link($id = null) {
        $addlink = helper::get_edit_tour_link($id);
        return \html_writer::link($addlink, get_string('newtour', 'tool_usertours'));
    }

    /**
     * Print the edit tour link.
     *
     * @param   int         $id     The ID of the tour
     */
    protected function print_edit_tour_link($id = null) {
        echo $this->get_edit_tour_link($id);
    }

    /**
     * Get the import tour link.
     *
     * @return string
     */
    protected function get_import_tour_link() {
        $importlink = helper::get_import_tour_link();
        return \html_writer::link($importlink, get_string('importtour', 'tool_usertours'));
    }

    /**
     * Print the edit tour page.
     *
     * @param   int         $id     The ID of the tour
     */
    protected function edit_tour($id = null) {
        global $PAGE;
        if ($id) {
            $tour = tour::instance($id);
            $PAGE->navbar->add($tour->get_name(), $tour->get_edit_link());

        } else {
            $tour = new tour();
            $PAGE->navbar->add(get_string('newtour', 'tool_usertours'), $tour->get_edit_link());
        }

        $form = new forms\edittour($tour);

        if ($form->is_cancelled()) {
            redirect(helper::get_list_tour_link());
        } else if ($data = $form->get_data()) {
            // Creating a new tour.
            $tour->set_name($data->name);
            $tour->set_description($data->description);
            $tour->set_pathmatch($data->pathmatch);
            $tour->set_enabled(!empty($data->enabled));

            foreach (configuration::get_defaultable_keys() as $key) {
                $tour->set_config($key, $data->$key);
            }

            // Save filter values.
            foreach (helper::get_all_filters() as $filterclass) {
                $filterclass::save_filter_values_from_form($tour, $data);
            }

            $tour->persist();

            redirect(helper::get_list_tour_link());
        } else {
            if (empty($tour)) {
                $this->header('newtour');
            } else {
                if (!empty($tour->get_config(self::CONFIG_SHIPPED_TOUR))) {
                    notification::add(get_string('modifyshippedtourwarning', 'tool_usertours'), notification::WARNING);
                }

                $this->header($tour->get_name());
                $data = $tour->prepare_data_for_form();

                // Prepare filter values for the form.
                foreach (helper::get_all_filters() as $filterclass) {
                    $filterclass::prepare_filter_values_for_form($tour, $data);
                }
                $form->set_data($data);
            }

            $form->display();
            $this->footer();
        }
    }

    /**
     * Print the export tour page.
     *
     * @param   int         $id     The ID of the tour
     */
    protected function export_tour($id) {
        $tour = tour::instance($id);

        // Grab the full data record.
        $export = $tour->to_record();

        // Remove the id.
        unset($export->id);

        // Set the version.
        $export->version = get_config('tool_usertours', 'version');

        // Step export.
        $export->steps = [];
        foreach ($tour->get_steps() as $step) {
            $record = $step->to_record();
            unset($record->id);
            unset($record->tourid);

            $export->steps[] = $record;
        }

        $exportstring = json_encode($export);

        $filename = 'tour_export_' . $tour->get_id() . '_' . time() . '.json';

        // Force download.
        send_file($exportstring, $filename, 0, 0, true, true);
    }

    /**
     * Handle tour import.
     */
    protected function import_tour() {
        global $PAGE;
        $PAGE->navbar->add(get_string('importtour', 'tool_usertours'), helper::get_import_tour_link());

        $form = new forms\importtour();

        if ($form->is_cancelled()) {
            redirect(helper::get_list_tour_link());
        } else if ($form->get_data()) {
            // Importing a tour.
            $tourconfigraw = $form->get_file_content('tourconfig');
            $tour = self::import_tour_from_json($tourconfigraw);

            redirect($tour->get_view_link());
        } else {
            $this->header();
            $form->display();
            $this->footer();
        }
    }

    /**
     * Print the view tour page.
     *
     * @param   int         $tourid     The ID of the tour to display.
     */
    protected function view_tour($tourid) {
        global $PAGE;
        $tour = helper::get_tour($tourid);

        $PAGE->navbar->add($tour->get_name(), $tour->get_view_link());

        $this->header($tour->get_name());
        echo \html_writer::span(get_string('viewtour_info', 'tool_usertours', [
                'tourname'  => $tour->get_name(),
                'path'      => $tour->get_pathmatch(),
            ]));
        echo \html_writer::div(get_string('viewtour_edit', 'tool_usertours', [
                'editlink'  => $tour->get_edit_link()->out(),
                'resetlink' => $tour->get_reset_link()->out(),
            ]));

        $table = new table\step_list($tourid);
        foreach ($tour->get_steps() as $step) {
            $table->add_data_keyed($table->format_row($step));
        }

        $table->finish_output();
        $this->print_edit_step_link($tourid);

        // JS for Step management.
        $PAGE->requires->js_call_amd('tool_usertours/managesteps', 'setup');

        $this->footer();
    }

    /**
     * Duplicate an existing tour.
     *
     * @param   int         $tourid     The ID of the tour to duplicate.
     */
    protected function duplicate_tour($tourid) {
        $tour = helper::get_tour($tourid);

        $export = $tour->to_record();
        // Remove the id.
        unset($export->id);

        // Set the version.
        $export->version = get_config('tool_usertours', 'version');

        $export->name = get_string('duplicatetour_name', 'tool_usertours', $export->name);

        // Step export.
        $export->steps = [];
        foreach ($tour->get_steps() as $step) {
            $record = $step->to_record();
            unset($record->id);
            unset($record->tourid);

            $export->steps[] = $record;
        }

        $exportstring = json_encode($export);
        $newtour = self::import_tour_from_json($exportstring);

        redirect($newtour->get_view_link());
    }

    /**
     * Show the tour.
     *
     * @param   int         $tourid     The ID of the tour to display.
     */
    protected function show_tour($tourid) {
        $this->show_hide_tour($tourid, 1);
    }

    /**
     * Hide the tour.
     *
     * @param   int         $tourid     The ID of the tour to display.
     */
    protected function hide_tour($tourid) {
        $this->show_hide_tour($tourid, 0);
    }

    /**
     * Show or Hide the tour.
     *
     * @param   int         $tourid     The ID of the tour to display.
     * @param   int         $visibility The intended visibility.
     */
    protected function show_hide_tour($tourid, $visibility) {
        global $DB;

        require_sesskey();

        $tour = $DB->get_record('tool_usertours_tours', array('id' => $tourid));
        $tour->enabled = $visibility;
        $DB->update_record('tool_usertours_tours', $tour);

        redirect(helper::get_list_tour_link());
    }

    /**
     * Delete the tour.
     *
     * @param   int         $tourid     The ID of the tour to remove.
     */
    protected function delete_tour($tourid) {
        require_sesskey();

        $tour = tour::instance($tourid);
        $tour->remove();

        redirect(helper::get_list_tour_link());
    }

    /**
     * Reset the tour state for all users.
     *
     * @param   int         $tourid     The ID of the tour to remove.
     */
    protected function reset_tour_for_all($tourid) {
        require_sesskey();

        $tour = tour::instance($tourid);
        $tour->mark_major_change();

        redirect(helper::get_view_tour_link($tourid), get_string('tour_resetforall', 'tool_usertours'));
    }

    /**
     * Get all tours for the current page URL.
     *
     * @param   bool        $reset      Forcibly update the current tours
     * @return  array
     */
    public static function get_current_tours($reset = false): array {
        global $PAGE;

        static $tours = false;

        if ($tours === false || $reset) {
            $tours = self::get_matching_tours($PAGE->url);
        }

        return $tours;
    }

    /**
     * Get all tours matching the specified URL.
     *
     * @param   moodle_url  $pageurl        The URL to match.
     * @return  array
     */
    public static function get_matching_tours(\moodle_url $pageurl): array {
        global $PAGE, $USER;

        // The following three checks make sure that the user is fully ready to use the site. If not, we do not show any tours.
        // We need the user to get properly set up so that all require_login() and other bits work as expected.

        if (user_not_fully_set_up($USER)) {
            return [];
        }

        if (get_user_preferences('auth_forcepasswordchange', false)) {
            return [];
        }

        if (empty($USER->policyagreed) && !is_siteadmin()) {
            $manager = new \core_privacy\local\sitepolicy\manager();

            if ($manager->is_defined(isguestuser())) {
                return [];
            }
        }

        $tours = cache::get_matching_tourdata($pageurl);

        $matches = [];
        if ($tours) {
            $filters = helper::get_all_filters();
            foreach ($tours as $record) {
                $tour = tour::load_from_record($record);
                if ($tour->is_enabled() && $tour->matches_all_filters($PAGE->context, $filters)) {
                    $matches[] = $tour;
                }
            }
        }

        return $matches;
    }

    /**
     * Import the provided tour JSON.
     *
     * @param   string      $json           The tour configuration.
     * @return  tour
     */
    public static function import_tour_from_json($json) {
        $tourconfig = json_decode($json);

        // We do not use this yet - we may do in the future.
        unset($tourconfig->version);

        $steps = $tourconfig->steps;
        unset($tourconfig->steps);

        $tourconfig->id = null;
        $tourconfig->sortorder = null;
        $tour = tour::load_from_record($tourconfig, true);
        $tour->persist(true);

        // Ensure that steps are orderered by their sortorder.
        \core_collator::asort_objects_by_property($steps, 'sortorder', \core_collator::SORT_NUMERIC);

        foreach ($steps as $stepconfig) {
            $stepconfig->id = null;
            $stepconfig->tourid = $tour->get_id();
            $step = step::load_from_record($stepconfig, true);
            $step->persist(true);
        }

        return $tour;
    }

    /**
     * Helper to fetch the renderer.
     *
     * @return  renderer
     */
    protected function get_renderer() {
        global $PAGE;
        return $PAGE->get_renderer('tool_usertours');
    }

    /**
     * Print the edit step link.
     *
     * @param   int     $tourid     The ID of the tour.
     * @param   int     $stepid     The ID of the step.
     * @return  string
     */
    protected function print_edit_step_link($tourid, $stepid = null) {
        $addlink = helper::get_edit_step_link($tourid, $stepid);
        $attributes = [];
        if (empty($stepid)) {
            $attributes['class'] = 'createstep';
        }
        echo \html_writer::link($addlink, get_string('newstep', 'tool_usertours'), $attributes);
    }

    /**
     * Display the edit step form for the specified step.
     *
     * @param   int     $id     The step to edit.
     */
    protected function edit_step($id) {
        global $PAGE;

        if (isset($id)) {
            $step = step::instance($id);
        } else {
            $step = new step();
            $step->set_tourid(required_param('tourid', PARAM_INT));
        }

        $tour = $step->get_tour();

        if (!empty($tour->get_config(self::CONFIG_SHIPPED_TOUR))) {
            notification::add(get_string('modifyshippedtourwarning', 'tool_usertours'), notification::WARNING);
        }

        $PAGE->navbar->add($tour->get_name(), $tour->get_view_link());
        if (isset($id)) {
            $PAGE->navbar->add($step->get_title(), $step->get_edit_link());
        } else {
            $PAGE->navbar->add(get_string('newstep', 'tool_usertours'), $step->get_edit_link());
        }

        $form = new forms\editstep($step->get_edit_link(), $step);
        if ($form->is_cancelled()) {
            redirect($step->get_tour()->get_view_link());
        } else if ($data = $form->get_data()) {
            $step->handle_form_submission($form, $data);
            $step->get_tour()->reset_step_sortorder();
            redirect($step->get_tour()->get_view_link());
        } else {
            if (empty($id)) {
                $this->header(get_string('newstep', 'tool_usertours'));
            } else {
                $this->header(get_string('editstep', 'tool_usertours', $step->get_title()));
            }
            $form->set_data($step->prepare_data_for_form());

            $form->display();
            $this->footer();
        }
    }

    /**
     * Move a tour up or down and redirect once complete.
     *
     * @param   int     $id     The tour to move.
     */
    protected function move_tour($id) {
        require_sesskey();

        $direction = required_param('direction', PARAM_INT);

        $tour = tour::instance($id);
        self::_move_tour($tour, $direction);

        redirect(helper::get_list_tour_link());
    }

    /**
     * Move a tour up or down.
     *
     * @param   tour    $tour   The tour to move.
     *
     * @param   int     $direction
     */
    protected static function _move_tour(tour $tour, $direction) {
        // We can't move the first tour higher, nor the last tour any lower.
        if (($tour->is_first_tour() && $direction == helper::MOVE_UP) ||
                ($tour->is_last_tour() && $direction == helper::MOVE_DOWN)) {

            return;
        }

        $currentsortorder   = $tour->get_sortorder();
        $targetsortorder    = $currentsortorder + $direction;

        $swapwith = helper::get_tour_from_sortorder($targetsortorder);

        // Set the sort order to something out of the way.
        $tour->set_sortorder(-1);
        $tour->persist();

        // Swap the two sort orders.
        $swapwith->set_sortorder($currentsortorder);
        $swapwith->persist();

        $tour->set_sortorder($targetsortorder);
        $tour->persist();
    }

    /**
     * Move a step up or down.
     *
     * @param   int     $id     The step to move.
     */
    protected function move_step($id) {
        require_sesskey();

        $direction = required_param('direction', PARAM_INT);

        $step = step::instance($id);
        $currentsortorder   = $step->get_sortorder();
        $targetsortorder    = $currentsortorder + $direction;

        $tour = $step->get_tour();
        $swapwith = helper::get_step_from_sortorder($tour->get_id(), $targetsortorder);

        // Set the sort order to something out of the way.
        $step->set_sortorder(-1);
        $step->persist();

        // Swap the two sort orders.
        $swapwith->set_sortorder($currentsortorder);
        $swapwith->persist();

        $step->set_sortorder($targetsortorder);
        $step->persist();

        // Reset the sort order.
        $tour->reset_step_sortorder();
        redirect($tour->get_view_link());
    }

    /**
     * Delete the step.
     *
     * @param   int         $stepid     The ID of the step to remove.
     */
    protected function delete_step($stepid) {
        require_sesskey();

        $step = step::instance($stepid);
        $tour = $step->get_tour();

        $step->remove();
        redirect($tour->get_view_link());
    }

    /**
     * Make sure all of the default tours that are shipped with Moodle are created
     * and up to date with the latest version.
     */
    public static function update_shipped_tours() {
        global $DB, $CFG;

        // A list of tours that are shipped with Moodle. They are in
        // the format filename => version. The version value needs to
        // be increased if the tour has been updated.
        $shippedtours = [
            '311_activity_information_activity_page_student.json' => 2,
            '311_activity_information_activity_page_teacher.json' => 2,
            '311_activity_information_course_page_student.json' => 2,
            '311_activity_information_course_page_teacher.json' => 2
        ];

        // These are tours that we used to ship but don't ship any longer.
        // We do not remove them, but we do disable them.
        $unshippedtours = [
            // Formerly included in Moodle 3.2.0.
            'boost_administrator.json' => 1,
            'boost_course_view.json' => 1,

            // Formerly included in Moodle 3.6.0.
            '36_dashboard.json' => 3,
            '36_messaging.json' => 3,
        ];

        $existingtourrecords = $DB->get_recordset('tool_usertours_tours');

        // Get all of the existing shipped tours and check if they need to be
        // updated.
        foreach ($existingtourrecords as $tourrecord) {
            $tour = tour::load_from_record($tourrecord);

            if (!empty($tour->get_config(self::CONFIG_SHIPPED_TOUR))) {
                $filename = $tour->get_config(self::CONFIG_SHIPPED_FILENAME);
                $version = $tour->get_config(self::CONFIG_SHIPPED_VERSION);

                // If we know about this tour (otherwise leave it as is).
                if (isset($shippedtours[$filename])) {
                    // And the version in the DB is an older version.
                    if ($version < $shippedtours[$filename]) {
                        // Remove the old version because it's been updated
                        // and needs to be recreated.
                        $tour->remove();
                    } else {
                        // The tour has not been updated so we don't need to
                        // do anything with it.
                        unset($shippedtours[$filename]);
                    }
                }

                if (isset($unshippedtours[$filename])) {
                    if ($version <= $unshippedtours[$filename]) {
                        $tour = tour::instance($tour->get_id());
                        $tour->set_enabled(tour::DISABLED);
                        $tour->persist();
                    }
                }
            }
        }
        $existingtourrecords->close();

        // Ensure we correct the sortorder in any existing tours, prior to adding latest shipped tours.
        helper::reset_tour_sortorder();

        foreach (array_reverse($shippedtours) as $filename => $version) {
            $filepath = $CFG->dirroot . "/{$CFG->admin}/tool/usertours/tours/" . $filename;
            $tourjson = file_get_contents($filepath);
            $tour = self::import_tour_from_json($tourjson);

            // Set some additional config data to record that this tour was
            // added as a shipped tour.
            $tour->set_config(self::CONFIG_SHIPPED_TOUR, true);
            $tour->set_config(self::CONFIG_SHIPPED_FILENAME, $filename);
            $tour->set_config(self::CONFIG_SHIPPED_VERSION, $version);

            // Bump new tours to the top of the list.
            while ($tour->get_sortorder() > 0) {
                self::_move_tour($tour, helper::MOVE_UP);
            }

            if (defined('BEHAT_SITE_RUNNING') || (defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
                // Disable this tour if this is behat or phpunit.
                $tour->set_enabled(false);
            }

            $tour->persist();
        }
    }
}
