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
 * Matrix API to support version v1.1 of the Matrix specification.
 *
 * https://spec.matrix.org/v1.1/client-server-api/
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class v1p1 extends \communication_matrix\matrix_client {
    // Use the standard matrix API for these features.
    use features\matrix\create_room_v3;
    use features\matrix\get_room_members_v3;
    use features\matrix\remove_member_from_room_v3;
    use features\matrix\update_room_avatar_v3;
    use features\matrix\update_room_name_v3;
    use features\matrix\update_room_topic_v3;
    use features\matrix\upload_content_v3;
    use features\matrix\update_room_power_levels_v3;
    use features\matrix\get_room_powerlevels_from_sync_v3;
    use features\matrix\get_room_power_levels_v3;

    // We use the Synapse API here because it can invite users to a room without requiring them to accept the invite.
    use features\synapse\invite_member_to_room_v1;

    // User information and creation is a server-specific feature.
    use features\synapse\get_user_info_v2;
    use features\synapse\create_user_v2;
    use features\synapse\get_room_info_v1;
}
