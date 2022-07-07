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
 * Form related badges.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2018 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Form to edit badge details.
 *
 * @copyright 2018 Tung Thai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
class edit_relatedbadge_form extends moodleform {

    /**
     * Defines the form.
     */
    public function definition() {
        $mform = $this->_form;
        $badge = $this->_customdata['badge'];
        $badgesarray = $this->get_badges_option($badge);
        $mform->addElement('header', 'alignment', get_string('relatedbages', 'badges'));
        if (!$badgesarray) {
            $badgesarray = array(get_string('none'));
            $attributes = array('size' => '3', 'disabled' => true, 'style' => 'min-width: 200px');
        } else {
            $attributes = array('size' => '10');
        }
        $mform->addElement('select', 'relatedbadgeids', get_string('relatedbages', 'badges'), $badgesarray, $attributes);
        $mform->getElement('relatedbadgeids')->setMultiple(true);
        $this->add_action_buttons();

        // Freeze all elements if badge is active or locked.
        if ($badge->is_active() || $badge->is_locked()) {
            $mform->hardFreezeAllVisibleExcept(array());
        }
    }

    /**
     * Validates form data.
     *
     * @param array $data submitted data.
     * @param array $files submitted files.
     * @return array $errors An array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }

    /**
     * Return list badge of a course or list badge site.
     *
     * @param object $badge Badge object.
     * @return array $options An array the badges.
     */
    public function get_badges_option($badge) {
        global $DB;
        $sql = "SELECT b.id, b.name, b.version, b.language, b.type
                  FROM {badge} b
                 WHERE b.id <> :badgeid
                       AND b.id NOT IN (
                            SELECT DISTINCT b.id
                              FROM {badge_related} br
                              JOIN {badge} b ON (br.relatedbadgeid = b.id OR br.badgeid = b.id)
                             WHERE (br.badgeid = :badgeid2 OR br.relatedbadgeid = :badgeid3) AND b.id != :badgeid4)";
        $params = ['badgeid' => $badge->id, 'badgeid2' => $badge->id, 'badgeid3' => $badge->id, 'badgeid4' => $badge->id];
        if ($badge->type == BADGE_TYPE_COURSE) {
            $sql .= " AND (b.courseid = :courseid OR b.type = :badgetype)";
            $params['courseid'] = $badge->courseid;
            $params['badgetype'] = BADGE_TYPE_SITE;
        }

        $records = $DB->get_records_sql($sql, $params);
        $languages = get_string_manager()->get_list_of_languages();
        $options = array();
        foreach ($records as $record) {
            $language = isset($languages[$record->language]) ? $languages[$record->language] : '';
            $options[$record->id] = $record->name .
                ' (version: ' . $record->version . ', language: ' . $language . ', ' .
                ($record->type == BADGE_TYPE_COURSE ? get_string('badgesview', 'badges') : get_string('sitebadges', 'badges')) .
                ')';
        }
        return $options;
    }

}
