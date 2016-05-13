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
 * @package    mod_feedback
 * @copyright  2011 Rossiani Wijaya <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Feedback module conversion handler
 */
class moodle1_mod_feedback_handler extends moodle1_mod_handler {

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
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/FEEDBACK does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'feedback', '/MOODLE_BACKUP/COURSE/MODULES/MOD/FEEDBACK',
                array(
                    'renamefields' => array(
                        'summary' => 'intro',
                        'pageaftersub' => 'page_after_submit',
                    ),
                    'newfields' => array(
                        'autonumbering' => 1,
                        'site_after_submit' => '',
                        'introformat' => 0,
                        'page_after_submitformat' => 0,
                        'completionsubmit' => 0,
                    ),
                )
            ),
            new convert_path(
                'feedback_item', '/MOODLE_BACKUP/COURSE/MODULES/MOD/FEEDBACK/ITEMS/ITEM',
                array (
                    'newfields' => array(
                        'label' => '',
                        'options' => '',
                        'dependitem' => 0,
                        'dependvalue' => '',
                    ),
                )
            ),
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/FEEDBACK
     * data available
     */
    public function process_feedback($data) {
        global $CFG;

        // get the course module id and context id
        $instanceid     = $data['id'];
        $cminfo         = $this->get_cminfo($instanceid);
        $this->moduleid = $cminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // get a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_feedback');

        // convert course files embedded into the intro
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['intro'], $this->fileman);

        // Convert the introformat if necessary.
        if ($CFG->texteditors !== 'textarea') {
            $data['intro'] = text_to_html($data['intro'], false, false, true);
            $data['introformat'] = FORMAT_HTML;
        }

        // start writing feedback.xml
        $this->open_xml_writer("activities/feedback_{$this->moduleid}/feedback.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $this->moduleid,
            'modulename' => 'feedback', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('feedback', array('id' => $instanceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        $this->xmlwriter->begin_tag('items');

        return $data;
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/FEEDBACK/ITEMS/ITEM
     * data available
     */
    public function process_feedback_item($data) {
        $this->write_xml('item', $data, array('/item/id'));
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'feedback' path
     */
    public function on_feedback_end() {
        // finish writing feedback.xml
        $this->xmlwriter->end_tag('items');
        $this->xmlwriter->end_tag('feedback');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/feedback_{$this->moduleid}/inforef.xml");
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
