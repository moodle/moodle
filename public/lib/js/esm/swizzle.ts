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
 * Swizzle support types for Moodle React components.
 *
 * @module     core/swizzle
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Safety level for a swizzle action: stable, may change, or unsupported. */
export type SwizzleSafety = 'safe' | 'risky' | 'prohibited';

/** Independent eject/wrap safety levels for a swizzleable component. */
export type SwizzleActions = {
    eject: SwizzleSafety;
    wrap: SwizzleSafety;
};

/** Shape of a plugin's `js/esm/src/swizzle.json` — maps module names to safety levels. */
export type SwizzlePluginConfig = Record<string, SwizzleActions>;
