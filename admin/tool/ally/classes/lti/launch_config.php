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
 * Ally client config LTI launch configuration.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally\lti;

use report_allylti\local\launch_config as base_launch_config;
use stdClass;

/**
 * Ally client config LTI launch configuration.
 *
 * @package   tool_ally
 * @author    Guy Thomas
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class launch_config extends base_launch_config {

    /**
     * launch_config constructor.
     * @param stdClass|bool $pluginconfig
     * @param string    $report
     * @param           $cfg
     *
     * @throws \moodle_exception
     */
    public function __construct(stdClass $pluginconfig, stdClass $cfg) {
        $configured = !empty($pluginconfig) && !empty($pluginconfig->adminurl) && !empty($pluginconfig->key) &&
            !empty($pluginconfig->secret);

        if (!$configured) {
            throw new \moodle_exception('notconfigured', 'report_allylti');
        }

        $clientconfigurl = $pluginconfig->adminurl;
        // We really don't want another setting to fill in, so let's just fix up the one we already have.
        $clientconfigurl = str_replace('institution', 'clientconfig', $clientconfigurl);
        $this->url = new \moodle_url($clientconfigurl);
        $this->key = $pluginconfig->key;
        $this->secret = $pluginconfig->secret;

        $this->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED;
    }

}
