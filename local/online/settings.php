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
        require_once($ueslib);
        ues::require_extensions();

        require_once(dirname(__FILE__) . '/provider.php');

        $provider = new online_enrollment_provider(false);

        $reprocessurl = new moodle_url('/local/online/reprocess.php');

        $a = new stdClass;
        $a->reprocessurl = $reprocessurl->out(false);

        $settings = new admin_settingpage('local_online', $provider->get_name());
        $settings->add(
            new admin_setting_heading('local_online_header', '',
            get_string('pluginname_desc', 'local_online', $a))
        );

        $provider->settings($settings);

        $ADMIN->add('localplugins', $settings);
    }
}
