<?php
// This file is part of Moodle - http://moodle.org
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

namespace tool_ally\webservice;

use moodle_exception;
use tool_ally\auto_config;
use tool_ally\password;

/**
 * New trait for handling the user filling from the caught exception.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2020 Open LMS.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait user_fill_from_context_error {
    public static function validate_context($context) {
        try {
            parent::validate_context($context);
        } catch (moodle_exception $exception) {
            if ($exception->errorcode == 'usernotfullysetup') {
                $config = new auto_config();
                $webuserpwd = strval(new password());
                $config->configure_user($webuserpwd);
                parent::validate_context($context);
            } else {
                throw $exception;
            }
        }
    }
}
