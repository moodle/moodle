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
 * Tiny Loader for Moodle
 *
 * @module      editor_tiny/loader
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

let tinyMCEPromise;

import * as Config from 'core/config';

export const baseUrl = `${Config.wwwroot}/lib/editor/tiny/loader.php/${M.cfg.jsrev}`;

/**
 * Get the TinyMCE API Object.
 *
 * @returns {Promise<TinyMCE>} The TinyMCE API Object
 */
export const getTinyMCE = () => {
    if (tinyMCEPromise) {
        return tinyMCEPromise;
    }

    tinyMCEPromise = new Promise((resolve, reject) => {
        const head = document.querySelector('head');
        let script = head.querySelector('script[data-tinymce="tinymce"]');
        if (script) {
            resolve(window.tinyMCE);
        }

        script = document.createElement('script');
        script.dataset.tinymce = 'tinymce';
        script.src = `${baseUrl}/tinymce.js`;
        script.async = true;

        script.addEventListener('load', () => {
            resolve(window.tinyMCE);
        }, false);

        script.addEventListener('error', (err) => {
            reject(err);
        }, false);

        head.append(script);
    });

    return tinyMCEPromise;
};
