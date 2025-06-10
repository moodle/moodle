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
 * Class responsible for executing auto configuration for ally settings and web services.
 *
 * @package   tool_ally
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

use tool_ally\adminsetting\ally_configpasswordunmask;

/**
 * Class responsible for executing auto configuration for ally settings and web services.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auto_configurator {

    static protected $validconfigs = [
        'key' => PARAM_ALPHANUMEXT,
        'secret' => PARAM_RAW,
        'clientid' => PARAM_INT,
        'adminurl' => PARAM_URL,
        'pushurl' => PARAM_URL,
    ];

    /**
     * @param auto_config_resolver $resolver
     */
    public function configure_settings(auto_config_resolver $resolver) {
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');

        $configs = $resolver->resolve();

        foreach ($configs as $name => $value) {
            if ($name === 'secret') {
                $setting = new ally_configpasswordunmask('tool_ally/secret', '', '', '');
                $setting->write_setting($value);
            } else if (array_key_exists($name, self::$validconfigs)) {
                $value = clean_param($value, self::$validconfigs[$name]);
                set_config($name, $value, 'tool_ally');
            }
        }
    }

    /**
     * @param auto_config $wsconfig
     * @throws \Exception
     */
    public function configure_webservices(auto_config $wsconfig) {
        $wsconfig->configure();

        if (empty($wsconfig->user) || empty($wsconfig->token || empty($wsconfig->role))) {
            throw new \coding_exception('Web service configuration failed');
        }
    }
}
