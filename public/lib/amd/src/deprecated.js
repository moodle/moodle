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
 * The core/deprecated module allows you to mark things as deprecated and warn appropriately.
 *
 * @module     core/deprecated
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @example <caption>Mark something as deprecated</caption>
 * import emitDeprecation from 'core/deprecated';
 *
 * emitDeprecation('myFunction', {
 *     replacement: 'myNewFunction',
 *     since: '5.0',
 *     mdl: 'MDL-12345',
 * });
 */

import * as Cfg from 'core/config';
import {alert as emitNotice} from 'core/notification';

/**
 * Helper to get the message to display to users.
 *
 * @param {String} thing The name of the deprecated thing.
 * @param {String} [alternativeNotice] An alternative to the first part of the deprecation message.
 * This is intended to be used where the simple form is not suited.
 * @param {String} [replacement] The name of the replacement thing, if there is one.
 * @param {String} [since] The version since which this thing has been deprecated.
 * @param {String} [reason] The reason why this thing was deprecated.
 * @param {String} [mdl] The issue number with more information about this deprecation.
 * @returns {String} The message to display to users.
 */
const getMessage = (
    thing,
    alternativeNotice,
    replacement,
    since,
    reason,
    mdl,
) => {
    const stringParts = [];
    stringParts.push(`Deprecation: `);

    if (alternativeNotice) {
        stringParts.push(alternativeNotice);
    } else {
        stringParts.push(`${thing} has been deprecated`);
    }

    if(since !== null) {
        stringParts.push(` since ${since}`);
    }

    stringParts.push('.');

    if (reason) {
        stringParts.push(` ${reason}`);
    }

    if (replacement) {
        stringParts.push(` Please use ${replacement} instead.`);
    }

    if (mdl) {
        stringParts.push(` See ${mdl} for more information.`);
    }

    return stringParts.join('');
};

/**
 * Helper to get the message to display to users.
 *
 * @param {String} thing The name of the deprecated thing.
 * @param {String} [alternativeNotice] An alternative to the first part of the deprecation message.
 * This is intended to be used where the simple form is not suited.
 * @param {String} [replacement] The name of the replacement thing, if there is one.
 * @param {String} [since] The version since which this thing has been deprecated.
 * @param {String} [reason] The reason why this thing was deprecated.
 * @param {String} [mdl] The issue number with more information about this deprecation.
 * @returns {String} The message to display to users.
 */
const getHTMLMessage = (
    thing,
    alternativeNotice,
    replacement,
    since,
    reason,
    mdl,
) => {
    const stringParts = [];

    stringParts.push(`<h2>Deprecation</h2>`);
    if (alternativeNotice) {
        stringParts.push(`<p>${alternativeNotice}`);
    } else {
        stringParts.push(`<p><code>${thing}</code> is deprecated`);
    }

    if (since !== null) {
        stringParts.push(` since ${since}`);
    }

    stringParts.push('.</p>');

    if (reason) {
        stringParts.push(`<p>${reason}</p>`);
    }

    if (replacement) {
        stringParts.push(`<p>Please use <code>${replacement}</code> instead.</p>`);
    }

    if (mdl) {
        const url = `https://moodle.atlassian.net/browse/${mdl}`;
        stringParts.push(`<p>See <a href="${url}" target="_blank" rel="noopener noreferrer">${mdl}</a> for more information.</p>`);
    }

    return stringParts.join('');
};

/**
 * Whether the thing being deprecated is in the ignore list or not.
 *
 * Items in the ignore list will only trigger the notification if they are finally deprecated,
 * but will still log the deprecation in the console.
 *
 * @param {string} thing
 * @returns {boolean}
 */
const isIgnored = (thing) => {
    const ignored = Cfg.deprecationignorelist || [];
    return ignored.includes(thing);
};

/**
 * Check environment conditions to determine if a message can be emitted.
 *
 * @returns Whether a message can be emitted
 */
const canEmit = () => {
    if (Cfg.developerdebug) {
        return true;
    }

    if (document.querySelector('body.behat-site')) {
        return true;
    }

    return false;
};

/**
 * Mark a thing as deprecated, emitting a warning to the console and optionally displaying a toast notification.
 *
 * @param {String} thing The name of the deprecated thing.
 * @param {Object} options
 * @param {String} [options.alternativeNotice] An alternative to the first part of the deprecation message.
 * This is intended to be used where the simple form is not suited.
 * @param {String} [options.replacement] The name of the replacement thing, if there is one.
 * @param {String} [options.since] The version since which this thing has been deprecated.
 * @param {String} [options.reason] The reason why this thing was deprecated.
 * @param {String} [options.mdl] The issue number with more information about this deprecation.
 * @param {Boolean} [options.final=false] Whether this deprecation is final,
 *                  meaning it should throw an error instead of just emitting a warning.
 * @param {Boolean} [options.emit=true] Whether to emit a toast notification about this deprecation (in addition to
 */
export default function (thing, {
    alternativeNotice = null,
    replacement = null,
    since = null,
    reason = null,
    mdl = null,
    final = false,
    emit = true,
} = {}) {
    if (replacement === null && reason === null && mdl === null) {
        throw new Error('You must provide at least one of replacement, reason or mdl=false when marking something as deprecated.');
    }

    const message = getMessage(thing, alternativeNotice, replacement, since, reason, mdl);

    // Always emit if the deprecation is final.
    // For non-final deprecations emit if the message does not prevent it, and the user has not ignored it.
    // Non-final deprecations are only shown.
    if (final || canEmit()) {
        if (final || (emit && !isIgnored(thing))) {
            emitNotice('Deprecation Warning', getHTMLMessage(thing, alternativeNotice, replacement, since, reason, mdl));
        }
    }

    // If this is a final deprecation, throw an error to break the execution.
    if (final) {
        throw new Error(message);
    } else {
        console.error(message); // eslint-disable-line no-console
    }
}
