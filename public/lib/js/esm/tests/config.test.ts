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
 * Tests for the core/config ESM module.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import config, {isJSCachingEnabled} from '@moodle/lms/core/config';

describe('core/config', () => {
    it('exports the live M.cfg object', () => {
        expect(config).toBe((globalThis as any).M.cfg);
    });

    it('reflects default values from globalSetup', () => {
        expect(config.wwwroot).toBe('https://example.com');
        expect(config.apibase).toBe('https://example.com');
        expect(config.sesskey).toBe('test-sesskey');
        expect(config.language).toBe('en');
        expect(config.jsrev).toBe(-1);
        expect(config.userId).toBe(2);
        expect(config.traceId).toBe('test-trace-id');
    });

    it('reflects mutations to M.cfg', () => {
        (globalThis as any).M.cfg.language = 'fr';

        expect(config.language).toBe('fr');
    });

    it('reflects mutations via the config object', () => {
        config.language = 'de';

        expect((globalThis as any).M.cfg.language).toBe('de');
    });

    it('resets to defaults between tests', () => {
        // This test runs after the mutation tests above.
        // If beforeEach reset works, language should be back to 'en'.
        expect(config.language).toBe('en');
    });

    describe('isJSCachingEnabled', () => {
        it('is false when jsrev is -1 (developer mode)', () => {
            expect(config.jsrev).toBe(-1);
            expect(isJSCachingEnabled).toBe(false);
        });

        it('is true when jsrev is a positive revision number', () => {
            (globalThis as any).M.cfg.jsrev = 12345;

            jest.isolateModules(() => {
                const {isJSCachingEnabled: fresh} = require('@moodle/lms/core/config');
                expect(fresh).toBe(true);
            });
        });

        it('is true when jsrev is 0', () => {
            (globalThis as any).M.cfg.jsrev = 0;

            jest.isolateModules(() => {
                const {isJSCachingEnabled: fresh} = require('@moodle/lms/core/config');
                expect(fresh).toBe(true);
            });
        });
    });
});
