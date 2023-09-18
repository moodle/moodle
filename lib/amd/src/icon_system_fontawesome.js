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
 * An Icon System implementation for FontAwesome.
 *
 * @module core/icon_system_fontawesome
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from './ajax';
import LocalStorage from './localstorage';
import IconSystem from './icon_system';
import * as Mustache from './mustache';
import * as Config from './config';
import * as Url from './url';

/**
 * An set of properties for an icon.
 * @typedef {object} IconProperties
 * @property {array} attributes
 * @private
 */

/**
 * The FontAwesome icon system.
 */
export default class IconSystemFontawesome extends IconSystem {
    /**
     * @var {Map} staticMap A map of icon names to FA Icon.
     * @private
     */
    static staticMap = null;

    /**
     * @var {Promise} fetchPromise The promise used when fetching the result
     * @private
     */
    static fetchPromise = null;

    /**
     * @var {string} cacheKey The key used to store the icon map in LocalStorage.
     * @private
     */
    static cacheKey = `core_iconsystem/theme/${Config.theme}/core/iconmap-fontawesome`;

    /**
     * Prefetch resources so later calls to renderIcon can be resolved synchronously.
     *
     * @returns {Promise<IconSystemFontawesome>}
     */
    init() {
        if (IconSystemFontawesome.staticMap) {
            return Promise.resolve(this);
        }

        if (this.getMapFromCache()) {
            return Promise.resolve(this);
        }

        if (IconSystemFontawesome.fetchPromise) {
            return IconSystemFontawesome.fetchPromise;
        }

        return this.fetchMapFromServer();
    }

    /**
     * Get the icon map from LocalStorage.
     *
     * @private
     * @returns {Map}
     */
    getMapFromCache() {
        const map = LocalStorage.get(IconSystemFontawesome.cacheKey);
        if (map) {
            IconSystemFontawesome.staticMap = new Map(JSON.parse(map));
        }
        return IconSystemFontawesome.staticMap;
    }

    /**
     * Fetch the map data from the server.
     *
     * @private
     * @returns {Promise}
     */
    _fetchMapFromServer() {
        return fetchMany([{
            methodname: 'core_output_load_fontawesome_icon_system_map',
            args: {
                themename: Config.theme,
            },
        }], true, false, false, 0, Config.themerev)[0];
    }

    /**
     * Fetch the map data from the server.
     *
     * @returns {Promise<IconSystemFontawesome>}
     * @private
     */
    async fetchMapFromServer() {
        IconSystemFontawesome.fetchPromise = (async () => {
            const mapData = await this._fetchMapFromServer();

            IconSystemFontawesome.staticMap = new Map(Object.entries(mapData).map(([, value]) => ([
                `${value.component}/${value.pix}`,
                value.to,
            ])));
            LocalStorage.set(
                IconSystemFontawesome.cacheKey,
                JSON.stringify(Array.from(IconSystemFontawesome.staticMap.entries())),
            );

            return this;
        })();

        return IconSystemFontawesome.fetchPromise;
    }

    /**
     * Render an icon.
     *
     * @param {string} key
     * @param {string} component
     * @param {string} title
     * @param {string} template
     * @return {string} The rendered HTML content
     */
    renderIcon(key, component, title, template) {
        const iconKey = `${component}/${key}`;
        const mappedIcon = IconSystemFontawesome.staticMap.get(iconKey);
        const unmappedIcon = this.getUnmappedIcon(mappedIcon, key, component, title);

        const context = {
            title,
            unmappedIcon,
            alt: title,
            key: mappedIcon,
        };

        if (typeof title === "undefined" || title === '') {
            context['aria-hidden'] = true;
        }

        return Mustache.render(template, context).trim();
    }

    /**
     * Get the unmapped icon content, if the icon is not mapped.
     *
     * @param {IconProperties} mappedIcon
     * @param {string} key
     * @param {string} component
     * @param {string} title
     * @returns {IconProperties|null}
     * @private
     */
    getUnmappedIcon(mappedIcon, key, component, title) {
        if (mappedIcon) {
            return null;
        }

        return {
            attributes: [
                {name: 'src', value: Url.imageUrl(key, component)},
                {name: 'alt', value: title},
                {name: 'title', value: title}
            ],
        };
    }

    /**
     * Get the name of the template to pre-cache for this icon system.
     *
     * @return {string}
     * @method getTemplateName
     */
    getTemplateName() {
        return 'core/pix_icon_fontawesome';
    }
}
