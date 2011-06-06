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
 *
 * @package    mod
 * @subpackage glossary
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Glossary conversion handler
 */
class moodle1_mod_glossary_handler extends moodle1_mod_handler {

    /** @var moodle1_file_manager */
    protected $fileman = null;

    /** @var int cmid */
    protected $moduleid = null;

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/GLOSSARY does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'glossary', '/MOODLE_BACKUP/COURSE/MODULES/MOD/GLOSSARY',
                array(
                    'newfields' => array(
                        'introformat'       => FORMAT_MOODLE,
                        'completionentries' => 0,
                    ),
                )
            ),
            new convert_path('glossary_categories', '/MOODLE_BACKUP/COURSE/MODULES/MOD/GLOSSARY/CATEGORIES'),
            new convert_path(
                'glossary_category', '/MOODLE_BACKUP/COURSE/MODULES/MOD/GLOSSARY/CATEGORIES/CATEGORY',
                array(
                    'dropfields' => array(
                        'glossaryid'
                    )
                )
            )
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/GLOSSARY
     * data available
     */
    public function process_glossary($data) {
        global $CFG;

        // get the course module id and context id
        $instanceid     = $data['id'];
        $cminfo         = $this->get_cminfo($instanceid);
        $this->moduleid = $cminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // replay the upgrade step 2009042006
        if ($CFG->texteditors !== 'textarea') {
            $data['intro']       = text_to_html($data['intro'], false, false, true);
            $data['introformat'] = FORMAT_HTML;
        }

        // get a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_glossary');

        // convert course files embedded into the intro
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['intro'], $this->fileman);

        // start writing glossary.xml
        $this->open_xml_writer("activities/glossary_{$this->moduleid}/glossary.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $this->moduleid,
            'modulename' => 'glossary', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('glossary', array('id' => $instanceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        return $data;
    }

    /**
     * This is executed when the parser reaches the <CATEGORIES> opening element
     */
    public function on_glossary_categories_start() {
        $this->xmlwriter->begin_tag('categories');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/GLOSSARY/CATEGORIES/CATEGORY
     * data available
     */
    public function process_glossary_category($data) {
        $this->write_xml('category', $data, array('/category/id'));
    }

    /**
     * This is executed when the parser reaches the closing </CATEGORIES> element
     */
    public function on_glossary_categories_end() {
        $this->xmlwriter->end_tag('categories');
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'glossary' path
     */
    public function on_glossary_end() {
        // finalize glossary.xml
        $this->xmlwriter->end_tag('glossary');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/glossary_{$this->moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}
