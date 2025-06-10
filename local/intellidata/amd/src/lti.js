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
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    https://intelliboard.net/
 */
define(['jquery'], function($) {

    var resize = function() {
        var body = $("body");
        var doc = $(document);
        var frame = $("#contentframe");
        var padding = 15; //The bottom of the iframe wasn\'t visible on some themes. Probably because of border widths, etc.
        var lastHeight;
        var viewportHeight = body.height();

        if (lastHeight !== Math.min(doc.height(), viewportHeight)) {
            frame.css("height", viewportHeight - frame.offset().top - padding + "px");
            lastHeight = Math.min(doc.height(), body.height());
        }
    };

    return {
        init: function() {
            $(document).ready(function() {
                resize();
            });

            $(window).on("resize", resize);
        }
    };
});