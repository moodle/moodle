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
 * Browser location utilities.
 *
 * Provides a mockable abstraction over `window.location` for navigation actions.
 *
 * @module     core/location
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Navigate the browser to the given URL.
 *
 * This is equivalent to `window.location.assign(url)` but can be mocked in tests.
 *
 * @param url The URL to navigate to.
 */
/* istanbul ignore next */
export function redirect(url: string): void {
    window.location.assign(url);
}
