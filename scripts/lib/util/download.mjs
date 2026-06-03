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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Helper library to assist with downloads
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import https from "https";
import fs from "fs-extra";
import path from "path";

/**
 * Fetch the requested script, and save it to the output path.
 *
 * Handles redirects and errors.
 *
 * @param {string} url The URL to download the file from.
 * @param {string} filePath The path to save the downloaded file to.
 * @param {function} [modifier] Optional function to modify the file after download. Receives the file path as an argument.
 * @returns {Promise}
 */
export const download = (
    url,
    filePath,
    modifier,
) => {
    return new Promise((resolve, reject) => {
        https.get(url, { agent: false}, (response) => {
            // Handle redirect.
            if (
                response.statusCode >= 300 &&
                response.statusCode < 400 &&
                response.headers.location
            ) {
                response.resume();
                return download(response.headers.location, filePath, modifier)
                    .then(resolve)
                    .catch(reject);
            }

            if (response.statusCode !== 200) {
                return reject(
                    new Error(`Failed: ${url} (${response.statusCode})`)
                );
            }

            const fileDir = path.dirname(filePath);
            fs.mkdirSync(fileDir, { recursive: true });

            const file = fs.createWriteStream(filePath);
            response.pipe(file);

            file.on("finish", () => {
                file.close(() => {
                    if (modifier) {
                        modifier(filePath);
                    }
                    resolve();
                });
            });
            file.on("error", reject);
        }).on("error", reject);
    });
};
