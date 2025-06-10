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
namespace tool_ally\logging;

/**
 * Define logging constants.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class constants {
    const RANGE_NONE = 0;
    const RANGE_LIGHT = 1;
    const RANGE_MEDIUM = 2;
    const RANGE_ALL = 3;
    const SEV_EMERGENCY = 1000;
    const SEV_ALERT = 1001;
    const SEV_CRITICAL = 1002;
    const SEV_ERROR = 1003;
    const SEV_WARNING = 1004;
    const SEV_NOTICE = 1005;
    const SEV_INFO = 1006;
    const SEV_DEBUG = 1007;
}
