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
 * Events for the drawer.
 *
 * @module     core/drawer_events
 * @copyright  2019 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default {
    DRAWER_SHOWN: 'drawer-shown',
    DRAWER_HIDDEN: 'drawer-hidden',

    /**
     * Published by code that needs persistent drawers on a given side of the screen to get out of the
     * way while it has its own overlay open there. Drawer implementations may subscribe to close their
     * open drawers on that side in response, remembering which ones to reopen on
     * DRAWER_EXCLUSIVE_RELEASED.
     *
     * Payload: {region: 'left'|'right'} - which side is being requested.
     */
    DRAWER_EXCLUSIVE_REQUESTED: 'drawer-exclusive-requested',

    /**
     * Published once exclusive space on a side is no longer needed, so drawers closed for
     * DRAWER_EXCLUSIVE_REQUESTED on that side can reopen.
     *
     * Payload: {region: 'left'|'right'} - which side is being released. Must match the region used in
     * the corresponding DRAWER_EXCLUSIVE_REQUESTED call.
     */
    DRAWER_EXCLUSIVE_RELEASED: 'drawer-exclusive-released',
};
