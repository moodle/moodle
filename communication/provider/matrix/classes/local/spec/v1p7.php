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

namespace communication_matrix\local\spec;

/**
 * Matrix API to support version v1.7 of the Matrix specification.
 *
 * https://spec.matrix.org/v1.7/client-server-api/
 * https://spec.matrix.org/v1.7/changelog/#api-changes
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class v1p7 extends v1p6 {
    // Note: A new Content Upload API was introduced.
    // See details in the spec:
    // https://github.com/matrix-org/matrix-spec-proposals/pull/2246.
    use features\matrix\media_create_v1;
}
