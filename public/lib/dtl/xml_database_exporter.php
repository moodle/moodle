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
 * XML format exporter class
 *
 * @package    core_dtl
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * XML format exporter class.
 * Provides logic for writing XML tags and data inside appropriate callbacks.
 * Subclasses should define XML data sinks.
 */
abstract class xml_database_exporter extends database_exporter {
    /**
     * Generic output method. Subclasses should implement it with code specific
     * to the target XML sink.
     */
    abstract protected function output($text);

    /**
     * Callback function. Outputs open XML PI and moodle_database opening tag.
     *
     * @param float $version the version of the system which generating the data
     * @param string $release moodle release info
     * @param string $timestamp the timestamp of the data (in ISO 8601) format.
     * @param string $description a user description of the data.
     * @return void
     */
    public function begin_database_export($version, $release, $timestamp, $description) {
        $this->output('<?xml version="1.0" encoding="utf-8"?>');
        //TODO add xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" and schema information
        $this->output('<moodle_database version="'.$version.'" release="'.$release.'" timestamp="'.$timestamp.'"'.(empty ($description) ? '' : ' comment="'.htmlspecialchars($description, ENT_QUOTES, 'UTF-8').'"').'>');
    }

    /**
     * Callback function. Outputs table opening tag.
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     * @return void
     */
    public function begin_table_export(xmldb_table $table) {
        $this->output('<table name="'.$table->getName().'" schemaHash="'.$table->getHash().'">');
    }

    /**
     * Callback function. Outputs table closing tag.
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     */
    public function finish_table_export(xmldb_table $table) {
        $this->output('</table>');
    }

    /**
     * Callback function. Outputs moodle_database closing tag.
     */
    public function finish_database_export() {
        $this->output('</moodle_database>');
    }

    /**
     * Callback function. Outputs record tag with field subtags and data.
     *
     * @param xmldb_table $table - XMLDB object of the table from which data was retrieved
     * @param object $data - data object (fields and values from record)
     * @return void
     */
    public function export_table_data(xmldb_table $table, $data) {
        $this->output('<record>');
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $this->output('<field name="'.$key.'" value="null" />');
            } else {
                $this->output('<field name="'.$key.'">'.htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8').'</field>');
            }
        }
        $this->output('</record>');
    }
}
