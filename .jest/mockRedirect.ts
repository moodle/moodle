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
 * Global setup for Jest tests that provides a mock implementation of `location.redirect`
 * and a helper function `expectRedirect` for asserting expected redirects in tests.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as location from '@moodle/lms/core/location';

type ExpectRedirectOptions = {
    url?: string;
    urlContains?: string;
};

let redirectMatch: ExpectRedirectOptions | null = null;

const redirectSpy = jest.spyOn(location, 'redirect');

beforeEach(() => {
    redirectSpy.mockImplementation((url: string) => {
        // Throw an error to fail tests that trigger unexpected redirects.
        // Tests can override this with a no-op or custom implementation if they expect a redirect to happen (e.g. when testing AJAX requests that require login).
        throw new Error(`Unexpected redirect to URL: ${url}. If this is expected, please mock location.redirect in your test with the desired behavior.`);
    });

    (global as any).expectRedirect = ({
        url,
        urlContains,
    }: ExpectRedirectOptions): void => {
        redirectMatch = { url, urlContains };
        redirectSpy.mockImplementation((redirectUrl: string) => {
            if (url && redirectUrl === url) {
                return;
            }
            if (urlContains && redirectUrl.includes(urlContains)) {
                return;
            }

            throw new Error(`Expected redirect to URL: ${url ?? `containing ${urlContains}`}, but got redirect to URL: ${redirectUrl}`);
        });
    };
});

afterEach(() => {
    if (redirectMatch) {
        expect(redirectSpy).toHaveBeenCalledTimes(1);
    } else {
        expect(redirectSpy).not.toHaveBeenCalled();
    }

    redirectMatch = null;
});
