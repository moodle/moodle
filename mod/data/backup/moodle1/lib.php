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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 * Based off of a template @ http://docs.moodle.org/en/Development:Backup_1.9_conversion_for_developers
 *
 * @package    mod
 * @subpackage data
 * @copyright  2011 Aparup Banerjee <aparup@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Database conversion handler
 */
class moodle1_mod_data_handler extends moodle1_mod_handler {

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/DATA does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path('data', '/MOODLE_BACKUP/COURSE/MODULES/MOD/DATA',
                        array(
                            'newfields' => array(
                                'introformat' => 0,
                                'assesstimestart' => 0,
                                'assesstimefinish' => 0,
                            )
                        )
                    ),
            new convert_path('data_field', '/MOODLE_BACKUP/COURSE/MODULES/MOD/DATA/FIELDS/FIELD')
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/DATA
     * data available
     */
    public function process_data($data) {
        global $CFG;
        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid);
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        //upgrade data

        //Upgrade all the data->notification currently being NULL to 0
        if (is_null($data['notification'])) {
            $data['notification'] = 0;
        }

        //@todo: user data - upgrade content to new file storage

        // add 'export' tag to list and single template.
        $pattern = '/\#\#delete\#\#(\s+)\#\#approve\#\#/';
        $replacement = '##delete##$1##approve##$1##export##';
        $data['listtemplate'] = preg_replace($pattern, $replacement, $data['listtemplate']);
        $data['singletemplate'] = preg_replace($pattern, $replacement, $data['singletemplate']);

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea' && $data['introformat'] == FORMAT_MOODLE ) {
            $data['intro'] = text_to_html($data['intro'], false, false, true);
            $data['introformat'] = FORMAT_HTML;
        }

        //@todo: user data - move data comments to comments table
        //@todo: user data - move data ratings to ratings table

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/data_{$moduleid}/data.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'data', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('data', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        $addfield = true;
        foreach ($data as $field => $value) {
            if ($field == 'asearchtemplate') {
                //add field asearchtemplate (if doesn't exist already)
                $addfield = false;
            }
            $this->xmlwriter->full_tag($field, $value);
        }
        if ($addfield) {
            $this->xmlwriter->full_tag('asearchtemplate', null);
        }
        $this->xmlwriter->begin_tag('fields');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/DATA/FIELDS/FIELD
     * data available
     */
    public function process_data_field($data) {
        // process database fields
        $this->write_xml('field', $data, array('/field/id'));
    }
    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/DATA/RECORDS/RECORD
     * data available
     */
    public function process_data_record($data) {
        //@todo process user data, and define the convert path in get_paths() above.
        //$this->write_xml('record', $data, array('/record/id'));
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'data' path
     */
    public function on_data_end() {

        $this->xmlwriter->end_tag('fields');
        $this->xmlwriter->end_tag('data');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
    }
}
