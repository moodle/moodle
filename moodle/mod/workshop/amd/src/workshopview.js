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
 * Sets the equal height to the user plan widget boxes.
 *
 * @module      mod_workshop/workshopview
 * @category    output
 * @copyright   Loc Nguyen <loc.nguyendinh@harveynash.vn>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    /**
     * Sets the equal height to all elements in the group.
     *
     * @param {jQuery} group List of nodes.
     */
    function equalHeight(group) {
        var tallest = 0;
        group.height('auto');
        group.each(function() {
            var thisHeight = $(this).height();
            if (thisHeight > tallest) {
                tallest = thisHeight;
            }
        });
        group.height(tallest);
    }

    return /** @alias module:mod_workshop/workshopview */ {
        init: function() {
            var $dt = $('.path-mod-workshop .userplan dt');
            var $dd = $('.path-mod-workshop .userplan dd');
            equalHeight($dt);
            equalHeight($dd);
            $(window).on("resize", function() {
                equalHeight($dt);
                equalHeight($dd);
            });
        }
    };
});
