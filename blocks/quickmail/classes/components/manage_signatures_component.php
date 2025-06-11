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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\components;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\components\component;
use block_quickmail\persistents\signature;

class manage_signatures_component extends component implements \renderable {

    public $form;

    public $heading;

    public function __construct($params = []) {
        parent::__construct($params);

        // Get prepared form data, including appropriate handling of signature_editor.
        $preparedsignaturedata = $this->get_prepared_editor_signature_data($this->get_param('signature'));

        // Set the form.
        $this->form = $this->get_param('manage_signatures_form');

        // Set the form's default, prepared data.
        $this->form->set_data($preparedsignaturedata);

        $this->heading = false;
    }

    /**
     * Returns prepared form data, including appropriate handling of signature_editor
     *
     * @param  signature|null  $signature     signature persistent, or null
     * @return array
     */
    private function get_prepared_editor_signature_data($signature = null) {
        // If no signature was passed, create a temporary record belonging to this user.
        $persistent = ! empty($signature) ? $signature : new signature(0, (object) ['user_id' => $this->get_param('user')->id]);

        // Convert the signature to a simple object.
        $signaturerecord = $persistent->to_record();

        // Set this user's text editor preference.
        $signaturerecord->signatureformat = (int) $this->get_param('user')->mailformat;

        // Prepare the form data to include appropriate editor content.
        $preparedsignaturedata = file_prepare_standard_editor(
            $signaturerecord,
            'signature',
            \block_quickmail_config::get_editor_options($this->get_param('context')),
            $this->get_param('context'),
            \block_quickmail_plugin::$name,
            'signature',
            $signaturerecord->id
        );

        return $preparedsignaturedata;
    }

}
