/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @copyright Copyright (c) 2020 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Login visual effects.
 */
define(['jquery'],
    function($) {
        /**
         * AMD return object.
         */
        return {
            init: function(images) {
                let id = 0;
                if (images !== undefined && images.length > 1) {
                    setInterval(function () {
                        id = ((id === 3) || id >= images.length) ? 0 : id;
                        $('#page').css('background-image', 'url(' + images[id] + ')');
                        id++;
                    }, 5000);
                }
            }
        };
    }
);
