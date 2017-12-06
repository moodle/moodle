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
 * @package dataformview
 * @subpackage aligned
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformview_aligned_aligned extends mod_dataform\pluginbase\dataformview {

    protected $_editors = array('section');
    protected $_columns = null;
    protected $_entrytemplate = null;

    /**
     * Generates the default entry template for a new view instance or when reseting an existing instance.
     *
     * @return void
     */
    public function set_default_entry_template($content = null) {
        // Get all the fields.
        if (!$fields = $this->df->field_manager->get_fields()) {
            return;
        }

        if ($content === null) {
            // Set content.
            $content = '';
            // Author.
            $fieldname = get_string('fieldname', 'dataformfield_entryauthor');
            $content .= "[[$fieldname:picture]]\n[[$fieldname:name]]";
            // Fields.
            foreach ($fields as $field) {
                if ($field->id > 0) {
                    $fieldname = $field->name;
                    $content .= "\n[[$fieldname]]";
                }
            }
            // Actions.
            $fieldname = get_string('fieldname', 'dataformfield_entryactions');
            $content .= "\n[[$fieldname:edit]]\n[[$fieldname:delete]]";
        }
        $this->param2 = $content;
    }

    /**
     *
     */
    public function get_columns() {
        if (empty($this->_columns)) {
            $this->_columns = array();

            $columns = explode("\n", $this->entry_template);
            foreach ($columns as $column) {
                $column = trim($column);
                if (empty($column)) {
                    continue;
                }
                $arr = explode("|", $column);
                $tag = $arr[0];
                $header = !empty($arr[1]) ? $arr[1] : '';
                $class = !empty($arr[2]) ? $arr[2] : '';

                $definition = array($tag, $header, $class);
                $this->_columns[] = $definition;
            }
        }
        return $this->_columns;
    }

    /**
     * Subclass may need to override
     */
    public function replace_patterns_in_view($patterns, $replacements) {
        $this->param2 = str_replace($patterns, $replacements, $this->param2);

        parent::replace_patterns_in_view($patterns, $replacements);
    }

    /**
     *
     */
    protected function group_entries_definition($entriesset, $name = '') {
        global $OUTPUT;
        $elements = array();

        // Generate the header row.
        $tableheader = '';
        if ($this->has_headers()) {
            $columns = $this->get_columns();
            foreach ($columns as $column) {
                list(, $header, $class) = $column;
                $tableheader .= html_writer::tag('th', $header, array('class' => $class));
            }
            $tableheader = html_writer::tag('thead', html_writer::tag('tr', $tableheader));

        }
        // Open table and wrap header with thead.
        $elements[] = html_writer::start_tag('table', array('class' => 'generaltable')). $tableheader;

        // Flatten the set to a list of elements, wrap with tbody and close table.
        $elements[] = html_writer::start_tag('tbody');
        foreach ($entriesset as $entryid => $entrydefinitions) {
            $elements = array_merge($elements, $entrydefinitions);
        }
        $elements[] = html_writer::end_tag('tbody'). html_writer::end_tag('table');

        // Add group heading.
        $name = ($name == 'newentry') ? get_string('entrynew', 'dataform') : $name;
        if ($name) {
            array_unshift($elements, $OUTPUT->heading($name, 3, 'main'));
        }
        // Wrap with entriesview.
        array_unshift($elements, html_writer::start_tag('div', array('class' => 'entriesview')));
        array_push($elements, html_writer::end_tag('div'));

        return $elements;
    }

    /**
     *
     */
    protected function entry_definition($fielddefinitions, array $options = null) {
        $elements = array();
        // Get the columns definition from the view template.
        $columns = $this->get_columns();

        $htmlcontent = '';

        // Generate entry table row.
        $htmlcontent .= html_writer::start_tag('tr');
        foreach ($columns as $column) {
            list($tag, , $class) = array_map('trim', $column);
            if (isset($fielddefinitions[$tag])) {
                $fielddefinition = $fielddefinitions[$tag];
                if (!is_array($fielddefinition)) {
                    // Collect consecutive html to reduce number of elements.
                    $htmlcontent .= html_writer::tag('td', $fielddefinition, array('class' => $class));
                } else {
                    // Open cell, add html element and reset html content.
                    $htmlcontent .= html_writer::start_tag('td', array('class' => $class));
                    $elements[] = $htmlcontent;
                    $htmlcontent = '';
                    // Add the non html definition.
                    $elements[] = $fielddefinition;
                    // Close cell.
                    $htmlcontent .= html_writer::end_tag('td');
                }
            } else {
                $htmlcontent .= html_writer::tag('td', '', array('class' => $class));
            }
        }
        $htmlcontent .= html_writer::end_tag('tr');
        $elements[] = $htmlcontent;
        return $elements;
    }

    /**
     *
     */
    protected function new_entry_definition($entryid = -1) {
        $elements = array();

        // Get the columns definition from the view template.
        $columns = $this->get_columns();

        // Get field definitions for new entry.
        $fields = $this->get_fields();
        $entry = (object) array('id' => $entryid);
        $fielddefinitions = array();

        if ($fieldpatterns = $this->get_pattern_set('field')) {
            foreach ($fieldpatterns as $fieldid => $patterns) {
                $field = $fields[$fieldid];
                $options = array('edit' => true, 'manage' => true);
                if ($definitions = $field->get_definitions($patterns, $entry, $options)) {
                    $fielddefinitions = array_merge($fielddefinitions, $definitions);
                }
            }
        }

        // Generate entry table row.
        $elements[] = html_writer::start_tag('tr');
        foreach ($columns as $column) {
            list($tag, , $class) = array_map('trim', $column);
            if (!empty($fielddefinitions[$tag])) {
                $fielddefinition = $fielddefinitions[$tag];
                if (!is_array($fielddefinition)) {
                    $elements[] = html_writer::tag('td', $fielddefinition, array('class' => $class));
                } else {
                    $elements[] = html_writer::start_tag('td', array('class' => $class));
                    $elements[] = $fielddefinition;
                    $elements[] = html_writer::end_tag('td');
                }
            } else {
                $elements[] = html_writer::tag('td', '', array('class' => $class));
            }
        }
        $elements[] = html_writer::end_tag('tr');

        return $elements;
    }

    /**
     *
     */
    protected function has_headers() {
        foreach ($this->get_columns() as $column) {
            if (!empty($column[1])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the content of the view's entry template with text filters applied.
     *
     * @return string HTML fragment.
     */
    protected function get_entry_template() {
        if ($this->_entrytemplate === null) {
            $this->_entrytemplate = '';
            if ($this->param2) {
                // Apply text filters to template.
                $formatoptions = array(
                    'para' => false,
                    'allowid' => true,
                    'trusted' => true,
                    'noclean' => true
                );
                $this->_entrytemplate = format_text($this->param2, FORMAT_HTML, $formatoptions);
            }
        }
        return $this->_entrytemplate;
    }

    /**
     * Overriding parent to add param2 to templates text.
     *
     * @return string
     */
    protected function get_templates_text() {
        $text = parent::get_templates_text();
        $text .= ($this->param2 ? ' '. $this->param2 : '');

        return trim($text);;
    }
}
