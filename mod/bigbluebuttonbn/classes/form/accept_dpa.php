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

namespace mod_bigbluebuttonbn\form;

use context;
use moodle_exception;
use moodle_url;
use core_form\dynamic_form;
use mod_bigbluebuttonbn\local\config;

/**
 * Accept data processing agreement form presented before enabling the BigBlueButton activity module.
 *
 * @package    mod_bigbluebuttonbn
 * @copyright  2022 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accept_dpa extends dynamic_form {

    /**
     * Form definition
     */
    protected function definition() {
        $this->_form->addElement('html', \html_writer::tag('p',
            get_string('enablingbigbluebuttondpainfo', 'mod_bigbluebuttonbn', config::DEFAULT_DPA_URL)));
        $this->_form->addElement('checkbox', 'acceptdefaultdpa', false, get_string('acceptdpa', 'mod_bigbluebuttonbn'),
            ['class' => 'bold']);
        $this->_form->addRule('acceptdefaultdpa', get_string('required'), 'required', null, 'client');
    }

    /**
     * Return form context
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
          return \context_system::instance();
    }

    /**
     * Check if current user has access to this form, otherwise throw exception.
     *
     * @return void
     * @throws moodle_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('moodle/site:config', $this->get_context_for_dynamic_submission());
    }

    /**
     * Process the form submission, used if form was submitted via AJAX.
     *
     * @return array
     */
    public function process_dynamic_submission(): array {
        $result = false;
        $errors = [];

        if ($this->get_data()->acceptdefaultdpa) {
            try {
                set_config('bigbluebuttonbn_default_dpa_accepted', true);
                $result = true;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return [
            'result' => $result,
            'errors' => $errors,
        ];
    }

    /**
     * Load in existing data as form defaults (not applicable).
     *
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX.
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/admin/modules.php', ['show' => 'bigbluebuttonbn', 'sesskey' => sesskey()]);
    }
}
