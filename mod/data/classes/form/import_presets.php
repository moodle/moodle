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

namespace mod_data\form;

use context;
use moodle_exception;
use moodle_url;
use core_form\dynamic_form;

/**
 * Import presets form.
 *
 * @package    mod_data
 * @copyright  2022 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class import_presets extends dynamic_form {

    /**
     * Process the form submission
     *
     * @return array
     * @throws moodle_exception
     */
    public function process_dynamic_submission(): array {
        global $CFG;
        $filepath = $this->save_temp_file('importfile');
        $context = $this->get_context_for_dynamic_submission();
        $returnurl = new moodle_url('/mod/data/preset.php', [
            'id' => $context->instanceid,
            'action' => 'importzip',
            'filepath' => str_replace($CFG->tempdir, '', $filepath)
        ]);
        return [
            'result' => true,
            'url' => $returnurl->out(false),
        ];
    }

    /**
     * Get context
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        $cmid = $this->optional_param('cmid', null, PARAM_INT);
        $cm = get_coursemodule_from_id('data', $cmid);
        $context = \context_module::instance($cm->id);
        return $context;
    }

    /**
     * Set data
     *
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
        $data = (object) [
            'cmid' => $this->optional_param('cmid', 0, PARAM_INT),
        ];
        $this->set_data($data);
    }

    /**
     * Has access ?
     *
     * @return void
     * @throws moodle_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        if (!has_capability('mod/data:managetemplates', $this->get_context_for_dynamic_submission())) {
            throw new moodle_exception('importpresetmissingcapability', 'data');
        }
    }

    /**
     * Get page URL
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $cmid = $this->optional_param('cmid', null, PARAM_INT);
        return new moodle_url('/mod/data/preset.php', ['id' => $cmid]);
    }

    /**
     * Form definition
     *
     * @return void
     */
    protected function definition() {
        $mform = $this->_form;
        $mform->addElement('html', \html_writer::div(get_string('importpreset_desc', 'mod_data'), 'py-3'));
        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('filepicker', 'importfile', get_string('choosepreset', 'mod_data'), null,
            ['accepted_types' => '.zip']);
        $mform->addRule('importfile', null, 'required');
    }
}
