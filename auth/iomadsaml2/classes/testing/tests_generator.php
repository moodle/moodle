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
 * Methods common to Moodle and Totara tests generators
 *
 * @package   auth_iomadsaml2
 * @author    Noemie Ariste <noemie.ariste@catalyst.net.nz>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_iomadsaml2\testing;

defined('MOODLE_INTERNAL') || die();

trait tests_generator {

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->entitiescount = 0;
    }

    /**
     * Creates new IdP entity
     *
     * @param array|\stdClass $idprecord
     * @param bool $createfiles
     * @return \stdClass record from db
     */
    public function create_idp_entity($idprecord = [], $createfiles = true) : \stdClass {
        global $DB;
        // Add IdP and configuration.
        $entitycount = ++$this->entitiescount;
        if (!array_key_exists('metadataurl', $idprecord)) {
            $idprecord['metadataurl'] = 'https://idp.example.org/idp/shibboleth';
        }
        if (!array_key_exists('entityid', $idprecord)) {
            $idprecord['entityid'] = "https://idp{$entitycount}.example.org/idp/shibboleth";
        }
        if (!array_key_exists('defaultname', $idprecord)) {
            $idprecord['defaultname'] = "Test IdP {$entitycount}";
        }
        if (!array_key_exists('activeidp', $idprecord)) {
            $idprecord['activeidp'] = 1;
        }

        $recordid = $DB->insert_record('auth_iomadsaml2_idps', $idprecord);
        set_config('idpmetadata', $idprecord['metadataurl'], 'auth_iomadsaml2');
        if ($createfiles) {
            $auth = get_auth_plugin('iomadsaml2');
            touch($auth->certcrt);
            touch($auth->certpem);
            touch($auth->get_file(md5($idprecord['metadataurl']). ".idp.xml"));
        }
        return $DB->get_record('auth_iomadsaml2_idps', ['id' => $recordid]);
    }
}
