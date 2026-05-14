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
 * Tests for the core/url ESM module.
 *
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    fileUrl,
    imageUrl,
    relativeUrl,
} from '@moodle/lms/core/url';

declare const M: {
    cfg: {
        wwwroot: string;
        slasharguments: number;
        admin: string;
        sesskey: string;
    };
    util: {
        image_url: jest.Mock;
    };
};

describe('core/url', () => {
    beforeEach(() => {
        // eslint-disable-next-line camelcase
        M.util.image_url = jest.fn((imagename: string, component: string) => `${component}/${imagename}`);
        M.cfg.wwwroot = 'https://example.com';
        M.cfg.slasharguments = 1;
        M.cfg.admin = 'admin';
        M.cfg.sesskey = 'test-sesskey';
    });

    describe('fileUrl', () => {
        it('uses slasharguments URLs when enabled', () => {
            M.cfg.slasharguments = 1;

            expect(fileUrl('/pluginfile.php', 'a/b.txt')).toBe('https://example.com/pluginfile.php/a/b.txt');
        });

        it('uses query-string file parameter when slasharguments is disabled', () => {
            M.cfg.slasharguments = 0;

            expect(fileUrl('/pluginfile.php', 'a/b.txt')).toBe('https://example.com/pluginfile.php?file=%2Fa%2Fb.txt');
        });
    });

    describe('relativeUrl', () => {
        it('throws when passed absolute URLs', () => {
            expect(() => relativeUrl('https://example.org/foo')).toThrow('relativeUrl function does not accept absolute urls');
            expect(() => relativeUrl('http://example.org/foo')).toThrow('relativeUrl function does not accept absolute urls');
            expect(() => relativeUrl('ftp://example.org/foo')).toThrow('relativeUrl function does not accept absolute urls');
        });

        it('adds a leading slash when missing', () => {
            expect(relativeUrl('mod/forum/view.php')).toBe('https://example.com/mod/forum/view.php');
        });

        it('rewrites admin paths when admin dir is customised', () => {
            M.cfg.admin = 'adm';

            expect(relativeUrl('/admin/settings.php')).toBe('https://example.com/adm/settings.php');
        });

        it('appends query params', () => {
            expect(relativeUrl('/mod/forum/view.php', {id: 7, mode: 'all'})).toBe(
                'https://example.com/mod/forum/view.php?id=7&mode=all',
            );
        });

        it('adds sesskey when includeSessKey is true', () => {
            expect(relativeUrl('/mod/forum/post.php', {id: 42}, true)).toBe(
                'https://example.com/mod/forum/post.php?id=42&sesskey=test-sesskey',
            );
        });
    });

    describe('imageUrl', () => {
        it('delegates to M.util.image_url', () => {
            expect(imageUrl('t/edit', 'mod_feedback')).toBe('mod_feedback/t/edit');
            expect(M.util.image_url).toHaveBeenCalledWith('t/edit', 'mod_feedback');
        });
    });
});
