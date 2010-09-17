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
 * Iplookup utility functions
 *
 * @package    core
 * @subpackage iplookup
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.core_iplookup = {};

M.core_iplookup.init = function(Y, latitude, longitude) {
    if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        var point = new GLatLng(latitude, longitude);
        map.setCenter(point, 4);
        map.addOverlay(new GMarker(point));
        map.setMapType(G_HYBRID_MAP);

        Y.on('unload', function() {
            if (GBrowserIsCompatible()) {
                GUnload();
            }
        }, document.body);
    }
};
