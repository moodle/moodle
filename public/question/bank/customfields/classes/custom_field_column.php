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

namespace qbank_customfields;

use core_question\local\bank\column_base;
use core_question\local\bank\view;
use qbank_customfields\customfield\question_handler;

/**
 * A column type for the name of the question creator.
 *
 * @package     qbank_customfields
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_field_column extends column_base {

    /** @var \core_customfield\field_controller The custom field this column is displaying. */
    protected $field;

    /**
     * Constructor.
     *
     * @param view $qbank the question bank view we are helping to render.
     * @param \core_customfield\field_controller $field The custom field this column is displaying.
     */
    public function __construct(\core_question\local\bank\view $qbank, \core_customfield\field_controller $field) {
        parent::__construct($qbank);
        $this->field = $field;
    }

    public static function from_column_name(view $view, string $columnname, bool $ingoremissing = false): ?custom_field_column {
        $handler = question_handler::create();
        foreach ($handler->get_fields() as $field) {
            if ($field->get('shortname') == $columnname) {
                return new static($view, $field);
            }
        }
        if ($ingoremissing) {
            return null;
        } else {
            throw new \coding_exception('Custom field ' . $columnname . ' does not exist.');
        }
    }

    /**
     * Get the internal name for this column. Used as a CSS class name,
     * and to store information about the current sort. Must match PARAM_ALPHA.
     *
     * @return string column name.
     */
    public function get_name(): string {
        return 'customfield';
    }

    /**
     * Get the name of this column. This must be unique.
     * When using the inherited class to make many columns from one parent,
     * ensure each instance returns a unique value.
     *
     * @return string The unique name;
     */
    public function get_column_name(): string {
        return $this->field->get('shortname');
    }

    /**
     * Title for this column. Not used if is_sortable returns an array.
     *
     * @return string
     */
    public function get_title(): string {
        return $this->field->get_formatted_name();
    }

    /**
     * Output the contents of this column.
     *
     * @param object $question the row from the $question table, augmented with extra information.
     * @param string $rowclasses CSS class names that should be applied to this row of output.
     */
    protected function display_content($question, $rowclasses): void {
        $fieldhandler = $this->field->get_handler();
        if ($fieldhandler->can_view($this->field, $question->id)) {
            $fielddata = $fieldhandler->get_field_data($this->field, $question->id);
            echo $fieldhandler->display_custom_field_table($fielddata);
        } else {
            echo '';
        }
    }

    public function get_extra_classes(): array {
        return ['pe-3'];
    }

}
