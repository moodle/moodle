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

namespace tool_usertours;

use coding_exception;
use core\output\inplace_editable;

/**
 * Tour helper.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    tool_usertours
 */
class helper {
    /**
     * @var MOVE_UP
     */
    const MOVE_UP = -1;

    /**
     * @var MOVE_DOWN
     */
    const MOVE_DOWN = 1;

    /**
     * @var boolean Has it been bootstrapped?
     */
    private static $bootstrapped = false;

    /**
     * @var string Regex to check any matching lang string.
     */
    protected const LANG_STRING_REGEX = '|^([a-zA-Z][a-zA-Z0-9\.:/_-]*),([a-zA-Z][a-zA-Z0-9\.:/_-]*)$|';

    /**
     * Get the link to edit the step.
     *
     * If no stepid is specified, then a link to create a new step is provided. The $targettype must be specified in this case.
     *
     * @param   int     $tourid     The tour that the step belongs to.
     * @param   int     $stepid     The step ID.
     * @param   int     $targettype The type of step.
     *
     * @return \moodle_url
     */
    public static function get_edit_step_link($tourid, $stepid = null, $targettype = null) {
        $link = new \moodle_url('/admin/tool/usertours/configure.php');

        if ($stepid) {
            $link->param('action', manager::ACTION_EDITSTEP);
            $link->param('id', $stepid);
        } else {
            $link->param('action', manager::ACTION_NEWSTEP);
            $link->param('tourid', $tourid);
        }

        return $link;
    }

    /**
     * Get the link to move the tour.
     *
     * @param   int     $tourid     The tour ID.
     * @param   int     $direction  The direction to move in
     *
     * @return \moodle_url
     */
    public static function get_move_tour_link($tourid, $direction = self::MOVE_DOWN) {
        $link = new \moodle_url('/admin/tool/usertours/configure.php');

        $link->param('action', manager::ACTION_MOVETOUR);
        $link->param('id', $tourid);
        $link->param('direction', $direction);
        $link->param('sesskey', sesskey());

        return $link;
    }

    /**
     * Get the link to move the step.
     *
     * @param   int     $stepid     The step ID.
     * @param   int     $direction  The direction to move in
     *
     * @return \moodle_url
     */
    public static function get_move_step_link($stepid, $direction = self::MOVE_DOWN) {
        $link = new \moodle_url('/admin/tool/usertours/configure.php');

        $link->param('action', manager::ACTION_MOVESTEP);
        $link->param('id', $stepid);
        $link->param('direction', $direction);
        $link->param('sesskey', sesskey());

        return $link;
    }

    /**
     * Get the link ot create a new step.
     *
     * @param   int         $tourid     The ID of the tour to attach this step to.
     * @param   int         $targettype The type of target.
     *
     * @return  \moodle_url             The required URL.
     */
    public static function get_new_step_link($tourid, $targettype = null) {
        $link = new \moodle_url('/admin/tool/usertours/configure.php');
        $link->param('action', manager::ACTION_NEWSTEP);
        $link->param('tourid', $tourid);
        $link->param('targettype', $targettype);

        return $link;
    }

    /**
     * Get the link used to view the tour.
     *
     * @param   int         $tourid     The ID of the tour to display.
     * @return  \moodle_url             The URL.
     */
    public static function get_view_tour_link($tourid) {
        return new \moodle_url('/admin/tool/usertours/configure.php', [
                'id'        => $tourid,
                'action'    => manager::ACTION_VIEWTOUR,
            ]);
    }

    /**
     * Get the link used to reset the tour state for all users.
     *
     * @param   int         $tourid     The ID of the tour to display.
     * @return  \moodle_url             The URL.
     */
    public static function get_reset_tour_for_all_link($tourid) {
        return new \moodle_url('/admin/tool/usertours/configure.php', [
            'id'        => $tourid,
            'action'    => manager::ACTION_RESETFORALL,
            'sesskey'   => sesskey(),
        ]);
    }

    /**
     * Get the link used to edit the tour.
     *
     * @param   int         $tourid     The ID of the tour to edit.
     * @return  \moodle_url             The URL.
     */
    public static function get_edit_tour_link($tourid = null) {
        $link = new \moodle_url('/admin/tool/usertours/configure.php');

        if ($tourid) {
            $link->param('action', manager::ACTION_EDITTOUR);
            $link->param('id', $tourid);
        } else {
            $link->param('action', manager::ACTION_NEWTOUR);
        }

        return $link;
    }

    /**
     * Get the link used to import the tour.
     *
     * @return  \moodle_url             The URL.
     */
    public static function get_import_tour_link() {
        $link = new \moodle_url('/admin/tool/usertours/configure.php', [
                'action'    => manager::ACTION_IMPORTTOUR,
            ]);

        return $link;
    }

    /**
     * Get the link used to export the tour.
     *
     * @param   int         $tourid     The ID of the tour to export.
     * @return  \moodle_url             The URL.
     */
    public static function get_export_tour_link($tourid) {
        $link = new \moodle_url('/admin/tool/usertours/configure.php', [
            'action'    => manager::ACTION_EXPORTTOUR,
            'id'        => $tourid,
        ]);

        return $link;
    }

    /**
     * Get the link used to duplicate the tour.
     *
     * @param   int         $tourid     The ID of the tour to duplicate.
     * @return  \moodle_url             The URL.
     */
    public static function get_duplicate_tour_link($tourid) {
        $link = new \moodle_url('/admin/tool/usertours/configure.php', [
            'action'    => manager::ACTION_DUPLICATETOUR,
            'id'        => $tourid,
        ]);

        return $link;
    }

    /**
     * Get the link used to delete the tour.
     *
     * @param   int         $tourid     The ID of the tour to delete.
     * @return  \moodle_url             The URL.
     */
    public static function get_delete_tour_link($tourid) {
        return new \moodle_url('/admin/tool/usertours/configure.php', [
            'id'        => $tourid,
            'action'    => manager::ACTION_DELETETOUR,
            'sesskey'   => sesskey(),
        ]);
    }

    /**
     * Get the link for listing tours.
     *
     * @return  \moodle_url             The URL.
     */
    public static function get_list_tour_link() {
        $link = new \moodle_url('/admin/tool/usertours/configure.php');
        $link->param('action', manager::ACTION_LISTTOURS);

        return $link;
    }

    /**
     * Get a filler icon for display in the actions column of a table.
     *
     * @param   string      $url            The URL for the icon.
     * @param   string      $icon           The icon identifier.
     * @param   string      $alt            The alt text for the icon.
     * @param   string      $iconcomponent  The icon component.
     * @param   array       $options        Display options.
     * @return  string
     */
    public static function format_icon_link($url, $icon, $alt, $iconcomponent = 'moodle', $options = []) {
        global $OUTPUT;

        return $OUTPUT->action_icon(
            $url,
            new \pix_icon($icon, $alt, $iconcomponent, [
                'title' => $alt,
            ]),
            null,
            $options
        );
    }

    /**
     * Get a filler icon for display in the actions column of a table.
     *
     * @param   array       $options        Display options.
     * @return  string
     */
    public static function get_filler_icon($options = []) {
        global $OUTPUT;

        return \html_writer::span(
            $OUTPUT->pix_icon('t/filler', '', 'tool_usertours', $options),
            'action-icon'
        );
    }

    /**
     * Get the link for deleting steps.
     *
     * @param   int         $stepid     The ID of the step to display.
     * @return  \moodle_url             The URL.
     */
    public static function get_delete_step_link($stepid) {
        return new \moodle_url('/admin/tool/usertours/configure.php', [
            'action'    => manager::ACTION_DELETESTEP,
            'id'        => $stepid,
            'sesskey'   => sesskey(),
        ]);
    }

    /**
     * Render the inplace editable used to edit the tour name.
     *
     * @param tour $tour The tour to edit.
     * @return inplace_editable
     */
    public static function render_tourname_inplace_editable(tour $tour): inplace_editable {
        $name = format_text(static::get_string_from_input($tour->get_name()), FORMAT_HTML);
        return new inplace_editable(
            'tool_usertours',
            'tourname',
            $tour->get_id(),
            true,
            \html_writer::link(
                $tour->get_view_link(),
                $name
            ),
            $tour->get_name()
        );
    }

    /**
     * Render the inplace editable used to edit the tour description.
     *
     * @param tour $tour The tour to edit.
     * @return inplace_editable
     */
    public static function render_tourdescription_inplace_editable(tour $tour): inplace_editable {
        $description = format_text(static::get_string_from_input($tour->get_description()), FORMAT_HTML);
        return new inplace_editable(
            'tool_usertours',
            'tourdescription',
            $tour->get_id(),
            true,
            $description,
            $tour->get_description()
        );
    }

    /**
     * Render the inplace editable used to edit the tour enable state.
     *
     * @param tour $tour The tour to edit.
     * @return inplace_editable
     */
    public static function render_tourenabled_inplace_editable(tour $tour): inplace_editable {
        global $OUTPUT;

        if ($tour->is_enabled()) {
            $icon = 't/hide';
            $alt = get_string('disable');
            $value = 1;
        } else {
            $icon = 't/show';
            $alt = get_string('enable');
            $value = 0;
        }

        $editable = new inplace_editable(
            'tool_usertours',
            'tourenabled',
            $tour->get_id(),
            true,
            $OUTPUT->pix_icon($icon, $alt, 'moodle', [
                'title' => $alt,
            ]),
            $value
        );

        $editable->set_type_toggle();
        return $editable;
    }

    /**
     * Render the inplace editable used to edit the step name.
     *
     * @param step $step The step to edit.
     * @return inplace_editable
     */
    public static function render_stepname_inplace_editable(step $step): inplace_editable {
        $title = format_text(static::get_string_from_input($step->get_title()), FORMAT_HTML);

        return new inplace_editable(
            'tool_usertours',
            'stepname',
            $step->get_id(),
            true,
            \html_writer::link(
                $step->get_edit_link(),
                $title
            ),
            $step->get_title()
        );
    }

    /**
     * Get all of the tours.
     *
     * @return  stdClass[]
     */
    public static function get_tours() {
        global $DB;

        $tours = $DB->get_records('tool_usertours_tours', [], 'sortorder ASC');
        $return = [];
        foreach ($tours as $tour) {
            $return[$tour->id] = tour::load_from_record($tour);
        }
        return $return;
    }

    /**
     * Get the specified tour.
     *
     * @param   int         $tourid     The tour that the step belongs to.
     * @return  tour
     */
    public static function get_tour($tourid) {
        return tour::instance($tourid);
    }

    /**
     * Fetch the tour with the specified sortorder.
     *
     * @param   int         $sortorder  The sortorder of the tour.
     * @return  tour
     */
    public static function get_tour_from_sortorder($sortorder) {
        global $DB;

        $tour = $DB->get_record('tool_usertours_tours', ['sortorder' => $sortorder]);
        return tour::load_from_record($tour);
    }

    /**
     * Return the count of all tours.
     *
     * @return  int
     */
    public static function count_tours() {
        global $DB;

        return $DB->count_records('tool_usertours_tours');
    }

    /**
     * Reset the sortorder for all tours.
     */
    public static function reset_tour_sortorder() {
        global $DB;
        $tours = $DB->get_records('tool_usertours_tours', null, 'sortorder ASC, pathmatch DESC', 'id, sortorder');

        $index = 0;
        foreach ($tours as $tour) {
            if ($tour->sortorder != $index) {
                $DB->set_field('tool_usertours_tours', 'sortorder', $index, ['id' => $tour->id]);
            }
            $index++;
        }

        // Notify the cache that a tour has changed.
        // Tours are only stored in the cache if there are steps.
        // If there step count has changed for some reason, this will change the potential cache results.
        cache::notify_tour_change();
    }


    /**
     * Get all of the steps in the tour.
     *
     * @param   int         $tourid     The tour that the step belongs to.
     * @return  step[]
     */
    public static function get_steps($tourid) {
        $steps = cache::get_stepdata($tourid);

        $return = [];
        foreach ($steps as $step) {
            $return[$step->id] = step::load_from_record($step);
        }
        return $return;
    }

    /**
     * Fetch the specified step.
     *
     * @param   int         $stepid     The id of the step to fetch.
     * @return  step
     */
    public static function get_step($stepid) {
        return step::instance($stepid);
    }

    /**
     * Fetch the step with the specified sortorder.
     *
     * @param   int         $tourid     The tour that the step belongs to.
     * @param   int         $sortorder  The sortorder of the step.
     * @return  step
     */
    public static function get_step_from_sortorder($tourid, $sortorder) {
        global $DB;

        $step = $DB->get_record('tool_usertours_steps', ['tourid' => $tourid, 'sortorder' => $sortorder]);
        return step::load_from_record($step);
    }

    /**
     * Handle addition of the tour into the current page.
     */
    public static function bootstrap() {
        global $PAGE;

        if (!isloggedin() || isguestuser()) {
            return;
        }

        if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect'])) {
            // Do not try to show user tours inside iframe, in maintenance mode,
            // when printing, or during redirects.
            return;
        }

        if (self::$bootstrapped) {
            return;
        }
        self::$bootstrapped = true;

        $tours = manager::get_current_tours();

        if ($tours) {
            $filters = static::get_all_clientside_filters();

            $tourdetails = array_map(function ($tour) use ($filters) {
                return [
                    'tourId' => $tour->get_id(),
                    'startTour' => $tour->should_show_for_user(),
                    'filtervalues' => $tour->get_client_filter_values($filters),
                ];
            }, $tours);

            $filternames = self::get_clientside_filter_module_names($filters);

            $PAGE->requires->js_call_amd('tool_usertours/usertours', 'init', [
                $tourdetails,
                $filternames,
            ]);
        }
    }

    /**
     * Get the JS module names for the filters.
     *
     * @param array $filters
     * @return array
     * @throws coding_exception
     */
    public static function get_clientside_filter_module_names(array $filters): array {
        $filternames = [];
        foreach ($filters as $filter) {
            if ($component = \core_component::get_component_from_classname($filter)) {
                $filternames[] = sprintf(
                    "%s/filter_%s",
                    $component,
                    $filter::get_filter_name(),
                );
            } else {
                throw new \coding_exception("Could not determine component for filter class {$filter}");
            }
        }

        return $filternames;
    }

    /**
     * Get a list of all possible filters.
     *
     * @return  array
     */
    public static function get_all_filters() {
        $hook = new hook\before_serverside_filter_fetch(array_keys(
            \core_component::get_component_classes_in_namespace('tool_usertours', 'local\filter')
        ));
        \core\di::get(\core\hook\manager::class)->dispatch($hook);

        $filters = array_filter(
            $hook->get_filter_list(),
            function ($filterclass) {
                $rc = new \ReflectionClass($filterclass);
                return $rc->isInstantiable();
            }
        );

        $filters = array_merge($filters, static::get_all_clientside_filters());

        return $filters;
    }

    /**
     * Get a list of all clientside filters.
     *
     * @return  array
     */
    public static function get_all_clientside_filters() {
        $hook = new hook\before_clientside_filter_fetch(array_keys(
            \core_component::get_component_classes_in_namespace('tool_usertours', 'local\clientside_filter')
        ));
        \core\di::get(\core\hook\manager::class)->dispatch($hook);

        $filters = array_filter(
            $hook->get_filter_list(),
            function ($filterclass) {
                $rc = new \ReflectionClass($filterclass);
                return $rc->isInstantiable();
            }
        );

        return $filters;
    }

    /**
     * Attempt to fetch any matching langstring if the content is in the
     * format identifier,component.
     *
     * @param string $content Step's content or Tour's name or Tour's description
     * @return string Processed content, any langstring will be converted to translated text
     */
    public static function get_string_from_input(string $content): string {
        $content = trim($content);

        if (preg_match(static::LANG_STRING_REGEX, $content, $matches)) {
            if ($matches[2] === 'moodle') {
                $matches[2] = 'core';
            }

            if (get_string_manager()->string_exists($matches[1], $matches[2])) {
                $content = get_string($matches[1], $matches[2]);
            }
        }

        return $content;
    }

    /**
     * Check if the given string contains any matching langstring.
     *
     * @param string $string
     * @return bool
     */
    public static function is_language_string_from_input(string $string): bool {
        return preg_match(static::LANG_STRING_REGEX, $string) == true;
    }
}
