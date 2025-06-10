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

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ueslib = $CFG->dirroot . '/enrol/ues/publiclib.php';

    if (file_exists($ueslib)) {

        $_s = function($key, $a=null) {
            return get_string($key, 'local_lsu', $a);
        };

        require_once($ueslib);
        ues::require_extensions();

        require_once dirname(__FILE__) . '/provider.php';

        $provider = new lsu_enrollment_provider(false);

        $reprocessurl = new moodle_url('/local/lsu/reprocess.php');

        $a = new stdClass;
        $a->reprocessurl = $reprocessurl->out(false);

        $settings = new admin_settingpage('local_lsu', $provider->get_name());
        $settings->add(new admin_setting_heading('local_lsu_header', '', $_s('pluginname_desc', $a)));

        $provider->settings($settings);

        $ADMIN->add('localplugins', $settings);
    }
}
