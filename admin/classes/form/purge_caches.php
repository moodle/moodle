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
 * Form for selective purging of caches.
 *
 * @package    core
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_admin\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Form for selecting which caches to purge on admin/purgecaches.php
 *
 * @package   core
 * @copyright 2018 The Open University
 * @author    Mark Johnson <mark.johnson@open.ac.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purge_caches extends \moodleform {
    /**
     * Define a "Purge all caches" button, and a fieldset with checkboxes for selectively purging separate caches.
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('hidden', 'returnurl', $this->_customdata['returnurl']);
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->addElement('submit', 'all', get_string('purgecaches', 'admin'));
        $mform->addElement('header', 'purgecacheheader', get_string('purgeselectedcaches', 'admin'));
        $checkboxes = [
            $mform->createElement('advcheckbox', 'theme', '', get_string('purgethemecache', 'admin')),
            $mform->createElement('advcheckbox', 'courses', '', get_string('purgecoursecache', 'admin')),
            $mform->createElement('advcheckbox', 'lang', '', get_string('purgelangcache', 'admin')),
            $mform->createElement('advcheckbox', 'js', '', get_string('purgejscache', 'admin')),
            $mform->createElement('advcheckbox', 'template', '', get_string('purgetemplates', 'admin')),
            $mform->createElement('advcheckbox', 'filter', '', get_string('purgefiltercache', 'admin')),
            $mform->createElement('advcheckbox', 'muc', '', get_string('purgemuc', 'admin')),
            $mform->createElement('advcheckbox', 'other', '', get_string('purgeothercaches', 'admin'))
        ];
        $mform->addGroup($checkboxes, 'purgeselectedoptions');
        $mform->addElement('submit', 'purgeselectedcaches', get_string('purgeselectedcaches', 'admin'));
    }

    /**
     * If the "Purge selected caches" button was pressed, ensure at least one cache was selected.
     *
     * @param array $data
     * @param array $files
     * @return array Error messages
     */
    public function validation($data, $files) {
        $errors = [];
        if (isset($data['purgeselectedcaches']) && empty(array_filter($data['purgeselectedoptions']))) {
            $errors['purgeselectedoptions'] = get_string('purgecachesnoneselected', 'admin');
        }
        return $errors;
    }
}
