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
 * Form for manipulating with the template records.
 *
 * @package    quizaccess_seb
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb\local\form;

defined('MOODLE_INTERNAL') || die();

/**
 * Form for manipulating with the template records.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template extends \core\form\persistent {

    /** @var string Persistent class name. */
    protected static $persistentclass = 'quizaccess_seb\\template';

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('name', 'quizaccess_seb'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('textarea', 'description', get_string('description', 'quizaccess_seb'));
        $mform->setType('description', PARAM_TEXT);

        if ($this->get_persistent()->get('id')) {
            $mform->addElement('textarea', 'content', get_string('content', 'quizaccess_seb'), ['rows' => 20, 'cols' => 60]);
            $mform->addRule('content', get_string('required'), 'required');
        } else {
            $mform->addElement('filepicker', 'content', get_string('content', 'quizaccess_seb'));
            $mform->addRule('content', get_string('required'), 'required');
        }

        $mform->addElement('selectyesno', 'enabled', get_string('enabled', 'quizaccess_seb'));
        $mform->setType('enabled', PARAM_INT);

        if ($this->get_persistent()->get('id')) {
            $mform->hardFreezeAllVisibleExcept(['enabled']);
        }

        $this->add_action_buttons();
    }

    /**
     * Filter out the foreign fields of the persistent.
     *
     * @param \stdClass $data The data to filter the fields out of.
     * @return \stdClass.
     */
    protected function filter_data_for_persistent($data) {
        // Uploading a new template file.
        if (empty($this->get_persistent()->get('id'))) {
            $files = $this->get_draft_files('content');
            if ($files) {
                $file = reset($files);
                $data->content = $file->get_content();
            } else {
                // No file found. Remove content data and let persistent to return an error.
                unset($data->content);
            }
        }

        return parent::filter_data_for_persistent($data);
    }

    /**
     * Extra validation.
     *
     * @param  \stdClass $data Data to validate.
     * @param  array $files Array of files.
     * @param  array $errors Currently reported errors.
     * @return array of additional errors, or overridden errors.
     */
    protected function extra_validation($data, $files, array &$errors) {
        $newerrors = [];

        // Check name.
        if (empty($data->name)) {
            $newerrors['name'] = get_string('namerequired', 'quizaccess_seb');
        }

        return $newerrors;
    }
}
