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
 * IdP selection form.
 *
 * @package   auth_iomadsaml2
 * @author    Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2\form;

defined('MOODLE_INTERNAL') || die();

use moodleform;

require_once("$CFG->libdir/formslib.php");

/**
 * IdP selection form.
 *
 * @package    auth_iomadsaml2
 * @author     Rossco Hellmans <rosscohellmans@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class selectidp_dropdown extends moodleform {

    /**
     * Definition
     */
    public function definition() {
        $mform = $this->_form;

        $metadataentities = $this->_customdata['metadataentities'];
        $wants = $this->_customdata['wants'];
        $idpname = $this->_customdata['idpname'];

        $idpentityids = array_combine(array_keys($metadataentities), array_column($metadataentities, 'name'));

        $mform->addElement('hidden', 'wants', $wants);
        $mform->setType('wants', PARAM_URL);
        $mform->addElement('select', 'idp', '', $idpentityids);
        $mform->addElement('checkbox', 'rememberidp' , '', get_string('rememberidp', 'auth_iomadsaml2'));

        $mform->addElement('submit', 'login', $idpname, array('style' => 'margin-left:0px'));
    }

}

