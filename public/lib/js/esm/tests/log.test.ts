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
 * Tests for the core/log ESM module.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import log, {levels} from '@moodle/lms/core/log';

describe('log', () => {
    let consoleSpy: Record<string, jest.SpyInstance>;

    beforeEach(() => {
        consoleSpy = {
            trace: jest.spyOn(console, 'trace').mockImplementation(),
            debug: jest.spyOn(console, 'debug').mockImplementation(),
            info: jest.spyOn(console, 'info').mockImplementation(),
            warn: jest.spyOn(console, 'warn').mockImplementation(),
            error: jest.spyOn(console, 'error').mockImplementation(),
        };
        log.resetLevel();
    });

    afterEach(() => {
        Object.values(consoleSpy).forEach((spy) => spy.mockRestore());
    });

    describe('levels constant', () => {
        it('exports expected numeric values', () => {
            expect(levels.TRACE).toBe(0);
            expect(levels.DEBUG).toBe(1);
            expect(levels.INFO).toBe(2);
            expect(levels.WARN).toBe(3);
            expect(levels.ERROR).toBe(4);
            expect(levels.SILENT).toBe(5);
        });

        it('is also available on the default export', () => {
            expect(log.levels).toBe(levels);
        });
    });

    describe('level filtering', () => {
        it('defaults to WARN level', () => {
            expect(log.getLevel()).toBe(levels.WARN);
        });

        it('suppresses messages below current level', () => {
            log.setLevel(levels.WARN);
            log.trace('t');
            log.debug('d');
            log.info('i');
            expect(consoleSpy.trace).not.toHaveBeenCalled();
            expect(consoleSpy.debug).not.toHaveBeenCalled();
            expect(consoleSpy.info).not.toHaveBeenCalled();
        });

        it('outputs messages at or above current level', () => {
            log.setLevel(levels.WARN);
            log.warn('w');
            log.error('e');
            expect(consoleSpy.warn).toHaveBeenCalledWith('w');
            expect(consoleSpy.error).toHaveBeenCalledWith('e');
        });

        it('TRACE level shows everything', () => {
            log.setLevel(levels.TRACE);
            log.trace('t');
            log.debug('d');
            log.info('i');
            log.warn('w');
            log.error('e');
            expect(consoleSpy.trace).toHaveBeenCalled();
            expect(consoleSpy.debug).toHaveBeenCalled();
            expect(consoleSpy.info).toHaveBeenCalled();
            expect(consoleSpy.warn).toHaveBeenCalled();
            expect(consoleSpy.error).toHaveBeenCalled();
        });

        it('SILENT level suppresses everything', () => {
            log.setLevel(levels.SILENT);
            log.trace('t');
            log.debug('d');
            log.info('i');
            log.warn('w');
            log.error('e');
            expect(consoleSpy.trace).not.toHaveBeenCalled();
            expect(consoleSpy.debug).not.toHaveBeenCalled();
            expect(consoleSpy.info).not.toHaveBeenCalled();
            expect(consoleSpy.warn).not.toHaveBeenCalled();
            expect(consoleSpy.error).not.toHaveBeenCalled();
        });
    });

    describe('source parameter', () => {
        it('prefixes message with source when provided', () => {
            log.setLevel(levels.WARN);
            log.warn('something happened', 'mod_quiz');
            expect(consoleSpy.warn).toHaveBeenCalledWith('mod_quiz: something happened');
        });

        it('does not prefix when source is omitted', () => {
            log.setLevel(levels.ERROR);
            log.error('bare message');
            expect(consoleSpy.error).toHaveBeenCalledWith('bare message');
        });
    });

    describe('setLevel', () => {
        it('accepts numeric levels', () => {
            log.setLevel(levels.DEBUG);
            expect(log.getLevel()).toBe(levels.DEBUG);
        });

        it('accepts string level names (case-insensitive)', () => {
            log.setLevel('error' as 'ERROR');
            expect(log.getLevel()).toBe(levels.ERROR);
        });

        it('accepts uppercase string names', () => {
            log.setLevel('TRACE');
            expect(log.getLevel()).toBe(levels.TRACE);
        });
    });

    describe('setDefaultLevel and resetLevel', () => {
        afterEach(() => {
            log.setDefaultLevel(levels.WARN);
        });

        it('resetLevel reverts to the default', () => {
            log.setDefaultLevel(levels.ERROR);
            log.setLevel(levels.TRACE);
            expect(log.getLevel()).toBe(levels.TRACE);
            log.resetLevel();
            expect(log.getLevel()).toBe(levels.ERROR);
        });
    });

    describe('enableAll and disableAll', () => {
        it('enableAll sets level to TRACE', () => {
            log.enableAll();
            expect(log.getLevel()).toBe(levels.TRACE);
        });

        it('disableAll sets level to SILENT', () => {
            log.disableAll();
            expect(log.getLevel()).toBe(levels.SILENT);
        });
    });

    describe('setConfig', () => {
        it('sets level from config object', () => {
            log.setConfig({level: levels.INFO});
            expect(log.getLevel()).toBe(levels.INFO);
        });

        it('accepts string level in config', () => {
            log.setConfig({level: 'DEBUG'});
            expect(log.getLevel()).toBe(levels.DEBUG);
        });

        it('does nothing when level is not in config', () => {
            log.setLevel(levels.ERROR);
            log.setConfig({});
            expect(log.getLevel()).toBe(levels.ERROR);
        });
    });

    describe('log method alias', () => {
        it('log() is an alias for debug()', () => {
            log.setLevel(levels.DEBUG);
            log.log('test message', 'source');
            expect(consoleSpy.debug).toHaveBeenCalledWith('source: test message');
        });
    });

    describe('console method mapping', () => {
        beforeEach(() => {
            log.setLevel(levels.TRACE);
        });

        it('trace uses console.trace', () => {
            log.trace('msg');
            expect(consoleSpy.trace).toHaveBeenCalledWith('msg');
        });

        it('debug uses console.debug', () => {
            log.debug('msg');
            expect(consoleSpy.debug).toHaveBeenCalledWith('msg');
        });

        it('info uses console.info', () => {
            log.info('msg');
            expect(consoleSpy.info).toHaveBeenCalledWith('msg');
        });

        it('warn uses console.warn', () => {
            log.warn('msg');
            expect(consoleSpy.warn).toHaveBeenCalledWith('msg');
        });

        it('error uses console.error', () => {
            log.error('msg');
            expect(consoleSpy.error).toHaveBeenCalledWith('msg');
        });
    });
});
