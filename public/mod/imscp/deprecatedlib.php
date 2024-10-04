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
 * Callback methods for mod_imscp component
 *
 * @package     mod_imscp
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @deprecated since Moodle 4.3
 */
#[\core\attribute\deprecated(null, reason: 'It is no longer used', since: '4.3', mdl: 'MDL-76953', final: true)]
function imscp_libxml_disable_entity_loader() {
    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);
}
