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

namespace local_launchpad;

/**
 * Named failure during a structure setup operation (for CLI reporting).
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class operation_failure extends \RuntimeException {

    /** @var string */
    public readonly string $operation;

    /** @var string */
    public readonly string $target;

    public function __construct(string $operation, string $target, ?\Throwable $previous = null) {
        $this->operation = $operation;
        $this->target = $target;
        $detail = $previous ? $previous->getMessage() : 'unknown error';
        parent::__construct(
            "Failed during {$operation} on {$target}: {$detail}",
            0,
            $previous
        );
    }
}
