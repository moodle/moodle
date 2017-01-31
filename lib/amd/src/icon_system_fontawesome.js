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
 * Competency rule points module.
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/icon_system', 'jquery', 'core/ajax', 'core/mustache', 'core/localstorage'],
        function(IconSystem, $, Ajax, Mustache, LocalStorage) {

    var staticMap = null;
    var fetchMap = null;

    /**
     * IconSystemFontawesome
     */
    var IconSystemFontawesome = function() {
        IconSystem.apply(this, arguments);
    };
    IconSystemFontawesome.prototype = Object.create(IconSystem.prototype);

    IconSystemFontawesome.prototype.init = function() {
        if (staticMap) {
            return $.when();
        }

        var map = LocalStorage.get('core/iconmap-fontawesome');
        if (map) {
            map = JSON.parse(map);
        }

        if (map) {
            staticMap = map;
            return $.when();
        }

        if (fetchMap === null) {
            fetchMap = Ajax.call([{
                methodname: 'core_output_load_icon_map',
                args: { 'system': 'fontawesome' }
            }], true, false)[0];
        }

        return fetchMap.then(function(map) {
            staticMap = {};
            $.each(map, function(index, value) {
                staticMap[value.component + '/' + value.pix] = value.to;
            }.bind(this));
            LocalStorage.set('core/iconmap-fontawesome', JSON.stringify(staticMap));
        }.bind(this));
    };

    /**
     * Render an icon.
     *
     * @param {String} key
     * @param {String} component
     * @param {String} title
     * @param {String} template
     * @return {String}
     * @method renderIcon
     */
    IconSystemFontawesome.prototype.renderIcon = function(key, component, title, template) { //eslint-disable-line no-unused-vars
        var mappedIcon = staticMap[component + '/' + key];
        if (typeof mappedIcon === "undefined") {
            mappedIcon = component + '/' + key;
        }

        var context = {
            key: mappedIcon,
            title: title
        };

        return Mustache.render(template, context);
    };

    return /** @alias module:core/icon_system_fontawesome */ IconSystemFontawesome;

});
