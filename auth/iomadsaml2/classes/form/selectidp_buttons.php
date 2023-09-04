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
class selectidp_buttons extends moodleform {

    /**
     * Definition
     */
    public function definition() {
        $mform = $this->_form;

        $metadataentities = $this->_customdata['metadataentities'];
        $storedchoiceidp = $this->_customdata['storedchoiceidp'];
        $wants = $this->_customdata['wants'];

        $mform->addElement('hidden', 'wants', $wants);
        $mform->setType('wants', PARAM_URL);
        $mform->addElement('checkbox', 'rememberidp' , '', get_string('rememberidp', 'auth_iomadsaml2'));

        foreach ($metadataentities as $idpentities) {
            if (isset($idpentities[$storedchoiceidp])) {
                $storedchoiceidp = $idpentities[$storedchoiceidp];
                $mform->addElement('html',
                    $this->get_idpbutton($storedchoiceidp, $storedchoiceidp->name, $storedchoiceidp->logo, true));
                $mform->addElement('html', '<hr>');
                unset($idpentities[$storedchoiceidp]);
            }

            foreach ($idpentities as $idpentityid => $idp) {
                $mform->addElement('html', $this->get_idpbutton($idpentityid, $idp->name, $idp->logo));
            }
        }
    }

    /**
     * Get IPD Button.
     *
     * @param string $idpentityid
     * @param string $idpname
     * @param string $logourl
     * @param bool $rememberedidp
     *
     * @return string
     */
    private function get_idpbutton($idpentityid, $idpname, $logourl, $rememberedidp = false) {
        $logo = !is_null($logourl) ? "<img src=\"{$logourl}\"> " : "";
        $extraclasses = $rememberedidp ? "rememberedidp" : "";
        return <<<EOD
<div class="fitem fitem_actionbuttons fitem_fsubmit ">
    <button value="{$idpentityid}" class="btn idpbtn {$extraclasses}" type="submit" name="idp">
        {$logo}{$idpname}
    </button>
</div>
EOD;
    }

}

