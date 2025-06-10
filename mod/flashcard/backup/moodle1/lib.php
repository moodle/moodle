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
 * @subpackage flashcard
 * @copyright  2013 Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Flashcard module conversion handler
 */
class moodle1_mod_flashcard_handler extends moodle1_mod_handler {

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
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/FLASHCARD does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'flashcard', '/MOODLE_BACKUP/COURSE/MODULES/MOD/FLASHCARD',
                array(
                    'renamefields' => array(
                        'summary' => 'intro',
                        'summaryformat' => 'introformat',
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
                'flashcard_deckdata', '/MOODLE_BACKUP/COURSE/MODULES/MOD/FLASHCARD/DECK/CARD',
                array (
                )
            ),
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/FEEDBACK
     * data available
     */
    public function process_flashcard($data) {
        // Get the course module id and context id.
        $instanceid     = $data['id'];
        $cminfo         = $this->get_cminfo($instanceid);
        $this->moduleid = $cminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // Get a fresh new file manager for this instance.
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_flashcard');

        // Convert course files embedded into the intro.
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['intro'], $this->fileman);

        // Start writing flashcard.xml.
        $this->open_xml_writer("activities/flashcard_{$this->moduleid}/flashcard.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $this->moduleid,
            'modulename' => 'flashcard', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('flashcard', array('id' => $instanceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        $this->xmlwriter->begin_tag('group_decks');

        return $data;
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/FEEDBACK/ITEMS/ITEM
     * data available
     */
    public function process_flashcard_deckdata($data) {
        $this->write_xml('deck', $data, array('/deck/id'));

        $contextid = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // Get a fresh new file manager for this instance.
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_flashcard');

        // Convert course files embedded into the intro.
        $this->fileman->filearea = 'answertext';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['answertext'], $this->fileman);

        // Convert course files embedded into the intro.
        $this->fileman->filearea = 'responsetext';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['responsetext'], $this->fileman);
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'flashcard' path
     */
    public function on_flashcard_end() {
        // Finish writing flashcard.xml.
        $this->xmlwriter->end_tag('group_deck');
        $this->xmlwriter->end_tag('flashcard');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // Write inforef.xml.
        $this->open_xml_writer("activities/flashcard_{$this->moduleid}/inforef.xml");
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
