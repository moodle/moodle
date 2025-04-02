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
 * Admin setting for communication_matrix that validates the user prefix that Matrix allow for.
 *
 * @package    communication_matrix
 * @copyright  2025 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class communication_matrix_admin_setting_user_prefix_configtext extends admin_setting_configtext {

    #[\Override]
    public function validate($data) {
        // Must contain at least one non-number.
        // Must not start with an underscore (_).
        // Must only contain a-z, 0-9, ., _, =, -, /.
        // Reference https://spec.matrix.org/latest/appendices/#user-identifiers.
        if (!preg_match('/^(?![0-9]+$)(?!_)[a-z0-9._=\/\-]+$/', $data)) {
            return get_string('matrixinvalidcharacter', 'communication_matrix');
        }

        return parent::validate($data);
    }
}
