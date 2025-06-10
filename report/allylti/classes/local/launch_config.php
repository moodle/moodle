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
 * Ally report LTI launch configuration.
 *
 * @package    report_allylti
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_allylti\local;

/**
 * Ally report LTI launch configuration.
 *
 * @package    report_allylti
 * @copyright  Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class launch_config {

    /**
     * @var \moodle_url
     */
    protected $url;

    /**
     * @var string
     */
    protected $key;

    /** @var string
     *
     */
    protected $secret;

    /**
     * @var int
     */
    protected $launchcontainer;

    /**
     * launch_config constructor.
     * @param \stdClass|bool $pluginconfig
     * @param string    $report
     * @param           $cfg
     *
     * @throws \moodle_exception
     */
    public function __construct($pluginconfig, $report, $cfg) {
        $urlconfigname = $report . 'url';
        $configured = !empty($pluginconfig) && !empty($pluginconfig->{$urlconfigname}) && !empty($pluginconfig->key) &&
                !empty($pluginconfig->secret);

        if (!$configured) {
            throw new \moodle_exception('notconfigured', 'report_allylti');
        }

        $this->url = new \moodle_url($pluginconfig->{$urlconfigname});
        $this->key = $pluginconfig->key;
        $this->secret = $pluginconfig->secret;

        $container = LTI_LAUNCH_CONTAINER_EMBED;
        if (!empty($cfg->report_allylti_launch_container)) {
            $container = $cfg->report_allylti_launch_container;
        }
        $this->launchcontainer = $container;
    }

    /**
     * @return string
     */
    public function get_url() {
        $reporttype = optional_param('reporttype', '', PARAM_ALPHANUM);
        switch ($reporttype) {
            case('course'):
                return preg_replace('/institution$/', 'instructor', $this->url);
        }

        return $this->url;

    }

    /**
     * @return string
     */
    public function get_key() {
        return $this->key;
    }

    /**
     * @return string
     */
    public function get_secret() {
        return $this->secret;
    }

    /**
     * @return int
     */
    public function get_launchcontainer() {
        return $this->launchcontainer;
    }

}
