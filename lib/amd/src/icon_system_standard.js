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
 * @module core/icon_system_standard
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/icon_system', 'core/url', 'core/mustache'],
        function(IconSystem, CoreUrl, Mustache) {

    /**
     * IconSystemStandard
     *
     * @class core/icon_system_standard
     */
    var IconSystemStandard = function() {
        IconSystem.apply(this, arguments);
    };
    IconSystemStandard.prototype = Object.create(IconSystem.prototype);

    /**
     * Render an icon.
     *
     * @method renderIcon
     * @param {String} key
     * @param {String} component
     * @param {String} title
     * @param {String} template
     * @return {String}
     */
    IconSystemStandard.prototype.renderIcon = function(key, component, title, template) {
        var url = CoreUrl.imageUrl(key, component);

        var templatecontext = {
            attributes: [
                {name: 'src', value: url},
                {name: 'alt', value: title},
                {name: 'title', value: title}
            ]
        };
        if (typeof title === "undefined" || title == "") {
            templatecontext.attributes.push({name: 'aria-hidden', value: 'true'});
        }

        var result = Mustache.render(template, templatecontext);
        return result.trim();
    };

    /**
     * Get the name of the template to pre-cache for this icon system.
     *
     * @return {String}
     * @method getTemplateName
     */
    IconSystemStandard.prototype.getTemplateName = function() {
        return 'core/pix_icon';
    };

    return IconSystemStandard;
});
