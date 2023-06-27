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
 * Javascript Module to help out when editing user is uploading a tile photo.
 *
 * @module edit_upload_image_helper
 * @package course/format
 * @subpackage tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 */

/* eslint space-before-function-paren: 0 */

define(["jquery"], function ($) {
    "use strict";

    return {
        init: function() {
            $(document).ready(function () {
                // If user clicks the file upload button, collapse the top section showing the existing image.
                $(".fp-btn-choose").click(function () {
                    $("#id_headertag").addClass("collapsed");
                    $("#id_guidance").addClass("collapsed");
                });
            });
        }
    };

});