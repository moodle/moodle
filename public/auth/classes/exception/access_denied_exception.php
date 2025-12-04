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

namespace core_auth\exception;

/**
 * An exception to describe the case where access has been denied to a resource.
 *
 * @package    core_auth
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class access_denied_exception extends \core\exception\moodle_exception implements
    \core\exception\response_aware_exception
{
    /**
     * Constructor for the access_denied Exception.
     *
     * @param \stdClass $user
     * @param null|\Throwable $previous
     */
    public function __construct(
        /** @var \stdClass $user */
        protected \stdClass $user,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            'accessdenied:' . $this->get_error_identifier(),
            'error',
            '',
            $user->username,
            $previous,
        );
    }

    /**
     * Get the error identifier for this exception.
     *
     * @return string
     */
    abstract protected function get_error_identifier(): string;

    #[\Override]
    public function get_response_classname(): string {
        return \core\router\response\access_denied_response::class;
    }
}
