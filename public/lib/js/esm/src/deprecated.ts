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
 * It emits a console error for non-final deprecations, or throws an Error for final ones.
 * When developer debugging is enabled (or running under Behat), a toast notification is
 * also displayed via core/notification.
 *
 * @module     core/deprecated
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example
 * import emitDeprecation from '@moodle/lms/core/deprecated';
 *
 * emitDeprecation('myFunction', {
 *     replacement: 'myNewFunction',
 *     since: '5.0',
 *     mdl: 'MDL-12345',
 * });
 */

import config from '@moodle/lms/core/config';
import {getString} from '@moodle/lms/core/stringUtils';
import {requireAsync} from '@moodle/lms/core/amd';

/** The part of the AMD core/notification module this module uses. */
type NotificationModule = {
    alert: (
        title: string | Promise<string>,
        message: string | Promise<string>,
        cancelText: string | Promise<string>,
    ) => Promise<unknown>;
};

/** Options accepted by {@link emitDeprecation}. */
type DeprecationOptions = {
    /** An alternative to the first part of the deprecation message. */
    alternativeNotice?: string | null;
    /** The name of the replacement, if there is one. */
    replacement?: string | null;
    /** The version since which this thing has been deprecated. */
    since?: string | null;
    /** The reason why this thing was deprecated. */
    reason?: string | null;
    /** The tracker issue number with more information. */
    mdl?: string | null;
    /** Whether this deprecation is final (throws an error). */
    final?: boolean;
    /** Whether to emit a toast notification. */
    emit?: boolean;
};

/** The subset of {@link DeprecationOptions} that appears in a deprecation message. */
type DeprecationMessage = Pick<
    DeprecationOptions, 'alternativeNotice' | 'replacement' | 'since' | 'reason' | 'mdl'
> & {
    /** The name of the deprecated thing. */
    thing: string;
};

/**
 * Whether an optional message part was supplied and is not empty.
 *
 * @param value The message part to test.
 * @returns True when the part has text to include.
 */
const hasText = (value: string | null | undefined): value is string => typeof value === 'string' && value !== '';

/**
 * Build a plain-text deprecation message.
 *
 * @param details The parts of the deprecation to describe.
 * @returns The assembled message.
 */
const getMessage = ({thing, alternativeNotice, replacement, since, reason, mdl}: DeprecationMessage): string => {
    const parts: string[] = ['Deprecation: '];

    if (hasText(alternativeNotice)) {
        parts.push(alternativeNotice);
    } else {
        parts.push(`${thing} has been deprecated`);
    }

    if (hasText(since)) {
        parts.push(` since ${since}`);
    }

    parts.push('.');

    if (hasText(reason)) {
        parts.push(` ${reason}`);
    }

    if (hasText(replacement)) {
        parts.push(` Please use ${replacement} instead.`);
    }

    if (hasText(mdl)) {
        parts.push(` See ${mdl} for more information.`);
    }

    return parts.join('');
};

/**
 * Build an HTML deprecation message for the toast notification.
 *
 * @param details The parts of the deprecation to describe.
 * @returns The assembled HTML message.
 */
const getHtmlMessage = ({thing, alternativeNotice, replacement, since, reason, mdl}: DeprecationMessage): string => {
    const parts: string[] = ['<h2>Deprecation</h2>'];

    if (hasText(alternativeNotice)) {
        parts.push(`<p>${alternativeNotice}`);
    } else {
        parts.push(`<p><code>${thing}</code> is deprecated`);
    }

    if (since !== null) {
        parts.push(` since ${since}`);
    }

    parts.push('.</p>');

    if (hasText(reason)) {
        parts.push(`<p>${reason}</p>`);
    }

    if (hasText(replacement)) {
        parts.push(`<p>Please use <code>${replacement}</code> instead.</p>`);
    }

    if (hasText(mdl)) {
        const url = `https://moodle.atlassian.net/browse/${mdl}`;
        parts.push(`<p>See <a href="${url}" target="_blank" rel="noopener noreferrer">${mdl}</a> for more information.</p>`);
    }

    return parts.join('');
};

/**
 * Whether the thing being deprecated is in the ignore list.
 *
 * Items in the ignore list will only trigger the notification if they are finally deprecated,
 * but will still log the deprecation in the console.
 */
const isIgnored = (thing: string): boolean => config.deprecationignorelist.includes(thing);

/**
 * Check environment conditions to determine if a notification can be emitted.
 */
const canEmit = (): boolean => {
    if (config.developerdebug) {
        return true;
    }

    if (document.querySelector('body.behat-site')) {
        return true;
    }

    return false;
};

/**
 * Mark a thing as deprecated, emitting a warning to the console and optionally
 * displaying a toast notification.
 *
 * @param thing The name of the deprecated thing.
 * @param options Deprecation details.
 */
export default function emitDeprecation(thing: string, {
    alternativeNotice,
    replacement,
    since,
    reason,
    mdl,
    final = false,
    emit = true,
}: DeprecationOptions = {}): void {
    if (!hasText(replacement) && !hasText(reason) && !hasText(mdl)) {
        throw new Error('You must provide at least one of replacement, reason or mdl when marking something as deprecated.');
    }

    const details: DeprecationMessage = {
        thing, alternativeNotice, replacement, since, reason, mdl,
    };
    const message = getMessage(details);

    // Always emit if the deprecation is final.
    // For non-final deprecations emit if the config allows it and the user has not ignored it.
    if ((final || canEmit()) && (final || (emit && !isIgnored(thing)))) {
        // The core/notification module is still AMD — load it dynamically. Its alert()
        // accepts a Promise for any of its arguments, so getString is passed unawaited.
        void requireAsync<NotificationModule>('core/notification')
            .then(notification => {
                void notification.alert('Deprecation Warning', getHtmlMessage(details), getString('ok'));
            });
    }

    // If this is a final deprecation, throw an error to break execution.
    if (final) {
        throw new Error(message);
    }

    console.error(message);
}
