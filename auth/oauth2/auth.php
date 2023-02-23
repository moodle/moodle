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
 * Open ID authentication.
 *
 * @package auth_oauth2
 * @copyright 2017 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for oauth2 authentication.
 *
 * @package auth_oauth2
 * @copyright 2017 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class auth_plugin_oauth2 extends \auth_oauth2\auth {

    /**
     * Test the various configured Oauth2 providers.
     */
    public function test_settings() {
        global $OUTPUT;

        $authplugin = get_auth_plugin('oauth2');
        $idps = $authplugin->loginpage_idp_list('');
        $templateidps = [];

        if (empty($idps)) {
            echo $OUTPUT->notification(get_string('noconfiguredidps', 'auth_oauth2'), 'notifyproblem');
            return;
        } else {
            foreach ($idps as $idp) {
                $idpid = $idp['url']->get_param('id');
                $sesskey = $idp['url']->get_param('sesskey');
                $testurl = new moodle_url('/auth/oauth2/test.php', ['id' => $idpid, 'sesskey' => $sesskey]);

                $templateidps[] = ['name' => $idp['name'], 'url' => $testurl->out(), 'iconurl' => $idp['iconurl']];
            }
            echo $OUTPUT->render_from_template('auth_oauth2/idps', ['idps' => $templateidps]);
        }
    }
}


