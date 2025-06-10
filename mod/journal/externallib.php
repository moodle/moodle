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
 * This page lists all the instances of journal in a particular course
 *
 * @package mod_journal
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * The mod_journal_external class.
 *
 * @package    mod_journal
 * @copyright  2022 Elearning Software SRL http://elearningsoftware.ro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_journal_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_entry_parameters() {
        return new external_function_parameters(
            array(
                'journalid' => new external_value(PARAM_INT, 'id of journal')
            )
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function get_entry_returns() {
        return new external_single_structure(
            array(
                'text' => new external_value(PARAM_RAW, 'journal text'),
                'modified' => new external_value(PARAM_INT, 'last modified time'),
                'rating' => new external_value(PARAM_FLOAT, 'teacher rating'),
                'comment' => new external_value(PARAM_RAW, 'teacher comment'),
                'teacher' => new external_value(PARAM_INT, 'id of teacher')
            )
        );
    }

    /**
     * Return one entry record from the database, including contents optionally.
     *
     * @param int $journalid Journal id
     * @return array of warnings and the entries
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_entry($journalid) {
        global $DB, $USER;

        $params = self::validate_parameters(self::get_entry_parameters(), array('journalid' => $journalid));

        if (! $cm = get_coursemodule_from_id('journal', $params['journalid'])) {
            throw new invalid_parameter_exception(get_string('incorrectcmid', 'journal'));
        }

        if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
            throw new invalid_parameter_exception(get_string('incorrectcourseid', 'journal'));
        }

        if (! $journal = $DB->get_record('journal', array('id' => $cm->instance))) {
            throw new invalid_parameter_exception(get_string('incorrectjournalid', 'journal'));
        }

        $context = \context_module::instance($cm->id);
        self::validate_context($context);;
        require_capability('mod/journal:addentries', $context);

        if ($entry = $DB->get_record('journal_entries', array('userid' => $USER->id, 'journal' => $journal->id))) {
            return array(
                'text' => $entry->text,
                'modified' => $entry->modified,
                'rating' => $entry->rating,
                'comment' => $entry->entrycomment,
                'teacher' => $entry->teacher
            );
        } else {
            return '';
        }
    }

    /**
     * Returns description of method parameters
     *
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function set_text_parameters() {
        return new external_function_parameters(
            array(
                'journalid' => new external_value(PARAM_INT, 'id of journal'),
                'text' => new external_value(PARAM_RAW, 'text to set'),
                'format' => new external_value(PARAM_INT, 'format of text')
            )
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.3
     */
    public static function set_text_returns() {
        return new external_value(PARAM_RAW, 'new text');
    }

    /**
     * Sets the text for the element
     *
     * @param int $journalid Journal ID
     * @param string $text Text parameter
     * @param string $format Format constant for the string
     */
    public static function set_text($journalid, $text, $format) {
        global $DB, $USER;

        $params = self::validate_parameters(
            self::set_text_parameters(),
            array('journalid' => $journalid, 'text' => $text, 'format' => $format)
        );

        if (! $cm = get_coursemodule_from_id('journal', $params['journalid'])) {
            throw new invalid_parameter_exception(get_string('incorrectcmid', 'journal'));
        }

        if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
            throw new invalid_parameter_exception(get_string('incorrectcourseid', 'journal'));
        }

        if (! $journal = $DB->get_record('journal', array('id' => $cm->instance))) {
            throw new invalid_parameter_exception(get_string('incorrectjournalid', 'journal'));
        }

        $context = \context_module::instance($cm->id);
        self::validate_context($context);;
        require_capability('mod/journal:addentries', $context);

        $entry = $DB->get_record('journal_entries', array('userid' => $USER->id, 'journal' => $journal->id));

        $timenow = time();
        $newentry = new \stdClass();
        $newentry->text = $params['text'];
        $newentry->format = $params['format'];
        $newentry->modified = $timenow;

        if ($entry) {
            $newentry->id = $entry->id;
            $DB->update_record('journal_entries', $newentry);
        } else {
            $newentry->userid = $USER->id;
            $newentry->journal = $journal->id;
            $newentry->id = $DB->insert_record('journal_entries', $newentry);
        }

        if ($entry) {
            // Trigger module entry updated event.
            $event = \mod_journal\event\entry_updated::create(array(
                'objectid' => $journal->id,
                'context' => $context
            ));
        } else {
            // Trigger module entry created event.
            $event = \mod_journal\event\entry_created::create(array(
                'objectid' => $journal->id,
                'context' => $context
            ));

        }
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('journal', $journal);
        $event->trigger();

        return $newentry->text;
    }
}
