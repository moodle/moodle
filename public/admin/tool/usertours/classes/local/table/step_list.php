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
 * Table to show the list of steps in a tour.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours\local\table;

defined('MOODLE_INTERNAL') || die();

use tool_usertours\helper;
use tool_usertours\tour;
use tool_usertours\step;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Table to show the list of steps in a tour.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class step_list extends \flexible_table {
    /**
     * @var     int     $tourid     The id of the tour.
     */
    protected $tourid;

    /**
     * Construct the table for the specified tour ID.
     *
     * @param   int     $tourid     The id of the tour.
     */
    public function __construct($tourid) {
        parent::__construct('steps');
        $this->tourid = $tourid;

        $baseurl = new \moodle_url('/tool/usertours/configure.php', [
                'id' => $tourid,
            ]);
        $this->define_baseurl($baseurl);

        // Column definition.
        $this->define_columns([
            'title',
            'content',
            'target',
            'actions',
        ]);

        $this->define_headers([
            get_string('title', 'tool_usertours'),
            get_string('content', 'tool_usertours'),
            get_string('target', 'tool_usertours'),
            get_string('actions', 'tool_usertours'),
        ]);

        $this->set_attribute('class', 'admintable table generaltable steptable');
        $this->setup();
    }

    /**
     * Format the current row's title column.
     *
     * @param   step    $step       The step for this row.
     * @return  string
     */
    protected function col_title(step $step) {
        global $OUTPUT;
        return $OUTPUT->render(helper::render_stepname_inplace_editable($step));
    }

    /**
     * Format the current row's content column.
     *
     * @param   step    $step       The step for this row.
     * @return  string
     */
    protected function col_content(step $step) {
        $content = $step->get_content();
        $systemcontext = \context_system::instance();
        $content = file_rewrite_pluginfile_urls(
            $content,
            'pluginfile.php',
            $systemcontext->id,
            'tool_usertours',
            'stepcontent',
            $step->get_id()
        );

        $content = helper::get_string_from_input($content);
        $content = step::get_step_image_from_input($content);

        return format_text($content, $step->get_contentformat());
    }

    /**
     * Format the current row's target column.
     *
     * @param   step    $step       The step for this row.
     * @return  string
     */
    protected function col_target(step $step) {
        return $step->get_target()->get_displayname();
    }

    /**
     * Format the current row's actions column.
     *
     * @param   step    $step       The step for this row.
     * @return  string
     */
    protected function col_actions(step $step) {
        $actions = [];

        if ($step->is_first_step()) {
            $actions[] = helper::get_filler_icon();
        } else {
            $actions[] = helper::format_icon_link($step->get_moveup_link(), 't/up', get_string('movestepup', 'tool_usertours'));
        }

        if ($step->is_last_step()) {
            $actions[] = helper::get_filler_icon();
        } else {
            $actions[] = helper::format_icon_link(
                $step->get_movedown_link(),
                't/down',
                get_string('movestepdown', 'tool_usertours')
            );
        }

        $actions[] = helper::format_icon_link($step->get_edit_link(), 't/edit', get_string('edit'));

        $actions[] = helper::format_icon_link($step->get_delete_link(), 't/delete', get_string('delete'), 'moodle', [
            'data-action'   => 'delete',
            'data-id'       => $step->get_id(),
        ]);

        return implode('&nbsp;', $actions);
    }
}
