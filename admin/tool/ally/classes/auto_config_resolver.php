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
 * Class responsible for resolving Ally tool configurations for the CLI script.
 *
 * @package   tool_ally
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

/**
 * Class responsible for resolving Ally tool configurations for the CLI script.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auto_config_resolver {

    /**
     * @var string
     */
    protected $env;

    /**
     * @var string
     */
    protected $clioption;

    public function __construct($clioption) {
        $this->clioption = $clioption;
        $this->env = getenv('MOODLE_TOOL_ALLY_AUTO_CONFIGS');
    }

    /**
     * @return array|\stdClass
     * @throws \coding_exception
     */
    public function resolve() {
        $configstr = '';
        if (!empty($this->clioption)) {
            $configstr = $this->clioption;
        } else if (!empty($this->env)) {
            $configstr = (string) $this->env;
        }

        if (empty($configstr)) {
            $msg = 'No configs supplied. You provide configs by using the \'configs\' CLI option' .
                ' or by setting them to MOODLE_TOOL_ALLY_AUTO_CONFIGS environment variable';
            throw new \coding_exception($msg);
        }

        $configs = json_decode($configstr, true);
        if (empty($configs)) {
            throw new \coding_exception('Config string was not valid');
        }

        return $configs;
    }
}
