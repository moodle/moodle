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
 * Output rendering for the plugin.
 *
 * @package     tool_messageinbound
 * @copyright   2014 Andrew Nicols
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Implements the plugin renderer
 *
 * @copyright 2014 Andrew Nicols
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_messageinbound_renderer extends plugin_renderer_base {

    /**
     * Render a table listing all of the Inbound Message handlers.
     *
     * @param array $handlers - list of all messageinbound handlers.
     * @return string HTML to output.
     */
    public function messageinbound_handlers_table(array $handlers) {
        global $CFG;

        $table = new html_table();
        $handlername = new html_table_cell(get_string('name', 'tool_messageinbound') . "\n" .
                html_writer::tag('span', get_string('classname', 'tool_messageinbound'), array('class' => 'handler-function')));

        // Prepare some of the rows with additional styling.
        $enabled = new html_table_cell(get_string('enabled', 'tool_messageinbound'));
        $enabled->attributes['class'] = 'state';
        $edit = new html_table_cell(get_string('edit', 'tool_messageinbound'));
        $edit->attributes['class'] = 'edit';
        $table->head  = array(
                $handlername,
                get_string('description', 'tool_messageinbound'),
                $enabled,
                $edit,
            );
        $table->attributes['class'] = 'admintable generaltable messageinboundhandlers';

        $yes = get_string('yes');
        $no = get_string('no');

        $data = array();

        // Options for description formatting.
        $descriptionoptions = new stdClass();
        $descriptionoptions->trusted = false;
        $descriptionoptions->noclean = false;
        $descriptionoptions->smiley = false;
        $descriptionoptions->filter = false;
        $descriptionoptions->para = true;
        $descriptionoptions->newlines = false;
        $descriptionoptions->overflowdiv = true;

        $editurlbase = new moodle_url('/admin/tool/messageinbound/index.php');
        foreach ($handlers as $handler) {
            $handlername = new html_table_cell($handler->name . "\n" .
                    html_writer::tag('span', $handler->classname, array('class' => 'handler-function')));
            $handlername->header = true;

            $editurl = new moodle_url($editurlbase, array('classname' => $handler->classname));
            $editlink = $this->action_icon($editurl, new pix_icon('t/edit',
                    get_string('edithandler', 'tool_messageinbound', $handler->classname)));

            // Prepare some of the rows with additional styling.
            $enabled = new html_table_cell($handler->enabled ? $yes : $no);
            $enabled->attributes['class'] = 'state';
            $edit = new html_table_cell($editlink);
            $edit->attributes['class'] = 'edit';

            // Add the row.
            $row = new html_table_row(array(
                        $handlername,
                        format_text($handler->description, FORMAT_MARKDOWN, $descriptionoptions),
                        $enabled,
                        $edit,
                    ));

            if (!$handler->enabled) {
                $row->attributes['class'] = 'disabled';
            }
            $data[] = $row;
        }
        $table->data = $data;
        return html_writer::table($table);
    }

}
