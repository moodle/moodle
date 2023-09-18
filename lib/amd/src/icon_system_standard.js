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
 * The Standard icon system.
 *
 * @module core/icon_system_standard
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import IconSystem from './icon_system';
import * as CoreUrl from './url';
import * as Mustache from './mustache';

/**
 * The Standard icon system.
 */
export default class IconSystemStandard extends IconSystem {

    /**
     * Render an icon.
     *
     * @param {string} key
     * @param {string} component
     * @param {string} title
     * @param {string} template
     * @return {string}
     */
    renderIcon(key, component, title, template) {
        const url = CoreUrl.imageUrl(key, component);

        const templatecontext = {
            attributes: [
                {name: 'src', value: url},
                {name: 'alt', value: title},
                {name: 'title', value: title},
            ]
        };
        if (typeof title === "undefined" || title == "") {
            templatecontext.attributes.push({name: 'aria-hidden', value: 'true'});
        }

        return Mustache.render(template, templatecontext).trim();
    }

    /**
     * Get the name of the template to pre-cache for this icon system.
     *
     * @return {string}
     */
    getTemplateName() {
        return 'core/pix_icon';
    }
}
