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
 * Load the site admin nav tree via ajax and render the response.
 *
 * @module     block_navigation/site_admin_loader
 * @package    core
 * @copyright  2015 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/config', 'block_navigation/ajax_response_renderer'],
        function($, ajax, config, renderer) {

    var SITE_ADMIN_NODE_TYPE = 71;
    var URL = config.wwwroot + '/lib/ajax/getsiteadminbranch.php';

    return {
        load: function(element) {
            element = $(element);
            var promise = $.Deferred();
            var data = {
                type: SITE_ADMIN_NODE_TYPE,
                sesskey: config.sesskey
            };
            var settings = {
                type: 'POST',
                dataType: 'json',
                data: data
            };

            $.ajax(URL, settings).done(function(nodes) {
                renderer.render(element, nodes);
                promise.resolve();
            });

            return promise;
        }
    };
});
