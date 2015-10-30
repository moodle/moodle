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
 * XML format importer class
 *
 * @package    core_dtl
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * XML format importer class (uses SAX for speed and low memory footprint).
 * Provides logic for parsing XML data and calling appropriate callbacks.
 * Subclasses should define XML data sources.
 */
abstract class xml_database_importer extends database_importer {
    protected $current_table;
    protected $current_row;
    protected $current_field;
    protected $current_data;
    protected $current_data_is_null;

    /**
     * Creates and setups a SAX parser. Subclasses should use this method to
     * create the XML parser.
     *
     * @return resource XML parser resource.
     */
    protected function get_parser() {
        $parser = xml_parser_create();
        xml_set_object($parser, $this);
        xml_set_element_handler($parser, 'tag_open', 'tag_close');
        xml_set_character_data_handler($parser, 'cdata');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        return $parser;
    }

    /**
     * Callback function. Called by the XML parser for opening tags processing.
     *
     * @param resource $parser XML parser resource.
     * @param string $tag name of opening tag
     * @param array $attributes set of opening tag XML attributes
     * @return void
     */
    protected function tag_open($parser, $tag, $attributes) {
        switch ($tag) {
            case 'moodle_database' :
                if (empty($attributes['version']) || empty($attributes['timestamp'])) {
                    throw new dbtransfer_exception('malformedxmlexception');
                }
                $this->begin_database_import($attributes['version'], $attributes['timestamp']);
                break;
            case 'table' :
                if (isset($this->current_table)) {
                    throw new dbtransfer_exception('malformedxmlexception');
                }
                if (empty($attributes['name']) || empty($attributes['schemaHash'])) {
                    throw new dbtransfer_exception('malformedxmlexception');
                }
                $this->current_table = $attributes['name'];
                $this->begin_table_import($this->current_table, $attributes['schemaHash']);
                break;
            case 'record' :
                if (isset($this->current_row) || !isset($this->current_table)) {
                    throw new dbtransfer_exception('malformedxmlexception');
                }
                $this->current_row = new stdClass();
                break;
            case 'field' :
                if (isset($this->current_field) || !isset($this->current_row)) {
                    throw new dbtransfer_exception('malformedxmlexception');
                }
                $this->current_field = $attributes['name'];
                $this->current_data = '';
                if (isset($attributes['value']) and $attributes['value'] === 'null') {
                    $this->current_data_is_null = true;
                } else {
                    $this->current_data_is_null = false;
                }
                break;
            default :
                throw new dbtransfer_exception('malformedxmlexception');
        }
    }

    /**
     * Callback function. Called by the XML parser for closing tags processing.
     *
     * @param resource $parser XML parser resource.
     * @param string $tag name of opening tag
     * @return void
     */
    protected function tag_close($parser, $tag) {
        switch ($tag) {
            case 'moodle_database' :
                $this->finish_database_import();
                break;

            case 'table' :
                $this->finish_table_import($this->current_table);
                $this->current_table = null;
                break;

            case 'record' :
                $this->import_table_data($this->current_table, $this->current_row);
                $this->current_row = null;
                break;

            case 'field' :
                $field = $this->current_field;
                if ($this->current_data_is_null) {
                    $this->current_row->$field = null;
                } else {
                    $this->current_row->$field = $this->current_data;
                }
                $this->current_field        = null;
                $this->current_data         = null;
                $this->current_data_is_null = null;
                break;

            default :
                throw new dbtransfer_exception('malformedxmlexception');
        }
    }

    /**
     * Callback function. Called by the XML parser for character data processing.
     *
     * @param resource $parser XML parser resource.
     * @param string $data character data to be processed
     * @return void
     */
    protected function cdata($parser, $data) {
        if (isset($this->current_field)) {
            $this->current_data .= $data;
        }
    }
}
