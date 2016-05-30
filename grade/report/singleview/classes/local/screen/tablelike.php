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
 * The gradebook simple view - base class for the table
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\screen;

use html_table;
use html_writer;
use stdClass;
use grade_item;
use grade_grade;
use gradereport_singleview\local\ui\bulk_insert;

defined('MOODLE_INTERNAL') || die;

/**
 * The gradebook simple view - base class for the table
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class tablelike extends screen {

    /** @var array $headers A list of table headers */
    protected $headers = array();

    /** @var array $initerrors A list of errors that mean we should not show the table */
    protected $initerrors = array();

    /** @var array $definition Describes the columns in the table */
    protected $definition = array();

    /**
     * Format a row of the table
     *
     * @param mixed $item
     * @return string
     */
    public abstract function format_line($item);

    /**
     * Get the summary for this table.
     *
     * @return string
     */
    public abstract function summary();

    /**
     * Get the table headers
     *
     * @return array
     */
    public function headers() {
        return $this->headers;
    }

    /**
     * Set the table headers
     *
     * @param array $overwrite New headers
     * @return tablelike This
     */
    public function set_headers($overwrite) {
        $this->headers = $overwrite;
        return $this;
    }

    /**
     * Get the list of errors
     *
     * @return array
     */
    public function init_errors() {
        return $this->initerrors;
    }

    /**
     * Set an error detected while building the page.
     *
     * @param string $mesg
     */
    public function set_init_error($mesg) {
        $this->initerrors[] = $mesg;
    }

    /**
     * Get the table definition
     *
     * @return array The definition.
     */
    public function definition() {
        return $this->definition;
    }

    /**
     * Set the table definition
     *
     * @param array $overwrite New definition
     * @return tablelike This
     */
    public function set_definition($overwrite) {
        $this->definition = $overwrite;
        return $this;
    }

    /**
     * Get a element to generate the HTML for this table row
     * @param array $line This is a list of lines in the table (modified)
     * @param grade_grade $grade The grade.
     * @return string
     */
    public function format_definition($line, $grade) {
        foreach ($this->definition() as $i => $field) {
            // Table tab index.
            $tab = ($i * $this->total) + $this->index;
            $classname = '\\gradereport_singleview\\local\\ui\\' . $field;
            $html = new $classname($grade, $tab);

            if ($field == 'finalgrade' and !empty($this->structure)) {
                $html .= $this->structure->get_grade_analysis_icon($grade);
            }

            // Singleview users without proper permissions should be presented
            // disabled checkboxes for the Exclude grade attribute.
            if ($field == 'exclude' && !has_capability('moodle/grade:manage', $this->context)){
                $html->disabled = true;
            }

            $line[] = $html;
        }
        return $line;
    }

    /**
     * Get the HTML for the whole table
     * @return string
     */
    public function html() {
        global $OUTPUT;

        if (!empty($this->initerrors)) {
            $warnings = '';
            foreach ($this->initerrors as $mesg) {
                $warnings .= $OUTPUT->notification($mesg);
            }
            return $warnings;
        }
        $table = new html_table();

        $table->head = $this->headers();

        $summary = $this->summary();
        if (!empty($summary)) {
            $table->summary = $summary;
        }

        // To be used for extra formatting.
        $this->index = 0;
        $this->total = count($this->items);

        foreach ($this->items as $item) {
            if ($this->index >= ($this->perpage * $this->page) &&
                $this->index < ($this->perpage * ($this->page + 1))) {
                $table->data[] = $this->format_line($item);
            }
            $this->index++;
        }

        $underlying = get_class($this);

        $data = new stdClass();
        $data->table = $table;
        $data->instance = $this;

        $buttonattr = array('class' => 'singleview_buttons submit');
        $buttonhtml = implode(' ', $this->buttons());

        $buttons = html_writer::tag('div', $buttonhtml, $buttonattr);
        $selectview = new select($this->courseid, $this->itemid, $this->groupid);

        $sessionvalidation = html_writer::empty_tag('input',
            array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));

        $html = $selectview->html();
        $html .= html_writer::tag('form',
            $buttons . html_writer::table($table) . $this->bulk_insert() . $buttons . $sessionvalidation,
            array('method' => 'POST')
        );
        $html .= $selectview->html();
        return $html;
    }

    /**
     * Get the HTML for the bulk insert form
     *
     * @return string
     */
    public function bulk_insert() {
        return html_writer::tag(
            'div',
            (new bulk_insert($this->item))->html(),
            array('class' => 'singleview_bulk')
        );
    }

    /**
     * Get the buttons for saving changes.
     *
     * @return array
     */
    public function buttons() {
        $save = html_writer::empty_tag('input', array(
            'type' => 'submit',
            'value' => get_string('save', 'gradereport_singleview'),
        ));

        return array($save);
    }
}
