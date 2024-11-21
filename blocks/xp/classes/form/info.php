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
 * Information page form.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\form;

use core_form\dynamic_form;

/**
 * Information page form.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class info extends dynamic_form {

    use dynamic_world_trait;

    /** @var string */
    protected $routename = 'infos';

    public function process_dynamic_submission() {
        $config = $this->get_world()->get_config();
        $data = $this->get_data();
        $config->set('enableinfos', $data->enableinfos);
        $config->set('instructions', $data->instructions['text']);
        $config->set('instructions_format', $data->instructions['format']);
    }

    public function set_data_for_dynamic_submission(): void {
        $config = $this->get_world()->get_config();
        $this->set_data([
            'contextid' => $this->get_world()->get_context()->id,
            'enableinfos' => $config->get('enableinfos'),
            'instructions' => [
                'text' => $config->get('instructions'),
                'format' => $config->get('instructions_format'),
            ],
        ]);
    }

    /**
     * Form definition.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('hidden', 'contextid', $this->get_world()->get_context()->id);
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('selectyesno', 'enableinfos', get_string('enableinfos', 'block_xp'));
        $mform->addHelpButton('enableinfos', 'enableinfos', 'block_xp');

        $mform->addElement('editor', 'instructions', get_string('instructions', 'block_xp'), ['rows' => 10]);
        $mform->addHelpButton('instructions', 'instructions', 'block_xp');
    }

}
