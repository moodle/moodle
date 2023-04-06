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
 * Renderer class for Qubits User
 *
 * @package    local_qubitsuser
 * @author     Qubits Dev Team
 * @copyright  2023 <https://www.yardstickedu.com/>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_qubitsuser_renderer extends plugin_renderer_base {

    public function manage_users(){
        global $OUTPUT;
        $siteid = optional_param('siteid', 0, PARAM_INT);    // Site id.
        $templatecontext = new Stdclass;
        $params = array("siteid" => $siteid, "returnto" => "userslisting");
        $templatecontext->adduser_url = new moodle_url("/local/qubitsuser/adduser.php", $params);
        $templatecontext->existuser_url = new moodle_url("/local/qubitsuser/assignexistinguser.php", $params);
        return $this->output->render_from_template('local_qubitsuser/manage_users', $templatecontext);
    }

    
    
}