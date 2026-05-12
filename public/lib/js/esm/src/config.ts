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
 * Typed access to the Moodle page configuration (`M.cfg`).
 *
 * This module exposes the same `M.cfg` object that is injected into every Moodle page
 * by the server-side renderer, but with a full TypeScript interface so that consuming
 * modules get autocompletion and compile-time type safety.
 *
 * The default export is the live `M.cfg` object — mutations made by tests or other code
 * are immediately visible to every consumer.
 *
 * @module     core/config
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

declare const M: {
    cfg: MoodleConfig;
};

/** The shape of the Moodle page configuration object (`M.cfg`). */
export interface MoodleConfig {
    /** The root URL of the Moodle site (e.g. `https://example.com/moodle`). */
    wwwroot: string;
    /** The base URL for the REST API. */
    apibase: string;
    /** The URL of the site's home page. */
    homeurl: string;
    /** The current session key, used for CSRF protection. */
    sesskey: string;
    /** Session timeout in seconds. */
    sessiontimeout: number;
    /** How many seconds before timeout to show a warning. */
    sessiontimeoutwarning: number;
    /** Cache-buster revision for theme assets. */
    themerev: number;
    /** Whether slash arguments are enabled (1) or not (0). */
    slasharguments: number;
    /** The name of the current theme. */
    theme: string;
    /** The AMD module name of the icon system implementation. */
    iconsystemmodule: string;
    /** Cache-buster revision for JavaScript assets (-1 in developer mode). */
    jsrev: number;
    /** The admin directory name (usually `'admin'`). */
    admin: string;
    /** Whether the theme supports SVG icons. */
    svgicons: boolean;
    /** The user's timezone string. */
    usertimezone: string;
    /** The current UI language code. */
    language: string;
    /** The current course ID (0 when outside a course). */
    courseId: number;
    /** The context ID of the current course (0 when outside a course). */
    courseContextId: number;
    /** The context ID of the current page. */
    contextid: number | null;
    /** The instance ID for the current context. */
    contextInstanceId: number;
    /** Cache-buster revision for language strings. */
    langrev: number;
    /** Cache-buster revision for Mustache templates. */
    templaterev: number;
    /** The site ID (usually 1). */
    siteId: number;
    /** The current user's ID. */
    userId: number;
    /** List of JS deprecation warnings to suppress. */
    deprecationignorelist: string[];
    /** OpenTelemetry trace parent ID. */
    traceId: string;
    /** Whether developer debug mode is enabled. Present only when true. */
    developerdebug?: boolean;
    /** Whether a Behat test is running. Present only when true. */
    behatsiterunning?: boolean;
}

/**
 * The live Moodle page configuration object.
 *
 * This is the same object as `M.cfg` — changes are reflected everywhere.
 */
const config: MoodleConfig = M.cfg;
export default config;

/**
 * Whether JavaScript caching is enabled, determined by the `jsrev` config value.
 *
 * When `jsrev` is -1, it indicates that JavaScript caching is disabled (that is, in developer mode),
 * while any non-negative value indicates that caching is enabled and the value is used as a cache-buster.
 *
 * @returns `true` if JavaScript caching is enabled, or `false` if it is disabled.
 * @see M.cfg.jsrev
 */
export const isJSCachingEnabled = config.jsrev !== -1;
