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

namespace local_ai_manager\form;

use local_ai_manager\base_instance;
use local_ai_manager\local\connector_factory;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing a connector instance.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_instance_form extends \moodleform {

    /** @var base_instance the connector instance to edit */
    private base_instance $connectorinstance;

    /**
     * Form definition.
     */
    public function definition() {
        $connectorname = $this->_customdata['connector'];

        $mform = &$this->_form;
        $factory = \core\di::get(connector_factory::class);
        if (!empty($this->_customdata['id'])) {
            $this->connectorinstance = $factory->get_connector_instance_by_id($this->_customdata['id']);
        } else {
            $this->connectorinstance = $factory->get_new_instance($connectorname);
        }
        $this->connectorinstance->edit_form_definition($mform, $this->_customdata);

        $this->add_action_buttons();

    }

    /**
     * Some extra validation.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files): array {
        return $this->connectorinstance->validation($data, $files);
    }
}
