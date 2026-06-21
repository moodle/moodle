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
 * Tests for the Storage ESM module.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Storage, {type StorageInterface} from '@moodle/lms/core/Storage';

declare const M: {
    cfg: {
        wwwroot: string;
        jsrev: number;
        currentlogin: number | null;
    };
};

/**
 * Create an in-memory mock of the Storage interface.
 */
function createMockStorage(): StorageInterface {
    const store = new Map<string, string>();
    return {
        get length() {
            return store.size;
        },
        key(index: number) {
            return [...store.keys()][index] ?? null;
        },
        getItem(key: string) {
            return store.get(key) ?? null;
        },
        setItem(key: string, value: string) {
            store.set(key, value);
        },
        removeItem(key: string) {
            store.delete(key);
        },
        clear() {
            store.clear();
        },
    };
}

describe('Storage', () => {
    describe('hashString', () => {
        it('returns 0 for an empty string', () => {
            expect(Storage.hashString('')).toBe(0);
        });

        it('returns a consistent hash for the same input', () => {
            const hash1 = Storage.hashString('hello');
            const hash2 = Storage.hashString('hello');
            expect(hash1).toBe(hash2);
        });

        it('returns different hashes for different inputs', () => {
            const hash1 = Storage.hashString('hello');
            const hash2 = Storage.hashString('world');
            expect(hash1).not.toBe(hash2);
        });
    });

    describe('when jsrev is -1 (developer mode)', () => {
        beforeEach(() => {
            M.cfg.jsrev = -1;
        });

        it('get returns null', () => {
            const storage = createMockStorage();
            const wrapper = new Storage(storage);
            expect(wrapper.get('foo')).toBeNull();
        });

        it('set returns false', () => {
            const storage = createMockStorage();
            const wrapper = new Storage(storage);
            expect(wrapper.set('foo', 'bar')).toBe(false);
        });
    });

    describe('when jsrev is a positive value (caching enabled)', () => {
        beforeEach(() => {
            M.cfg.jsrev = 12345;
        });

        it('stores and retrieves values', () => {
            const storage = createMockStorage();
            const wrapper = new Storage(storage);

            wrapper.set('mykey', 'myvalue');
            expect(wrapper.get('mykey')).toBe('myvalue');
        });

        it('returns null for missing keys', () => {
            const storage = createMockStorage();
            const wrapper = new Storage(storage);

            expect(wrapper.get('nonexistent')).toBeNull();
        });

        it('prefixes keys to avoid collisions', () => {
            const storage = createMockStorage();
            const wrapper = new Storage(storage);

            wrapper.set('mykey', 'myvalue');
            // The raw storage should NOT have 'mykey' as a direct key.
            expect(storage.getItem('mykey')).toBeNull();
            // But the prefixed key should exist.
            const hashSource = `${M.cfg.wwwroot}/${M.cfg.jsrev}`;
            const prefix = `${Storage.hashString(hashSource)}/`;
            expect(storage.getItem(`${prefix}mykey`)).toBe('myvalue');
        });

        it('clears storage when jsrev changes', () => {
            const storage = createMockStorage();

            // First wrapper with jsrev = 12345.
            const wrapper1 = new Storage(storage);
            wrapper1.set('preserved', 'value1');
            expect(wrapper1.get('preserved')).toBe('value1');

            // Bump jsrev — simulates a new deployment.
            M.cfg.jsrev = 99999;
            const wrapper2 = new Storage(storage);
            // The old value should be gone (storage was cleared).
            expect(wrapper2.get('preserved')).toBeNull();
        });

        it('clean() clears the underlying storage', () => {
            const storage = createMockStorage();
            const wrapper = new Storage(storage);

            wrapper.set('a', '1');
            wrapper.set('b', '2');
            wrapper.clean();
            expect(storage.length).toBe(0);
        });
    });

    describe('login validation', () => {
        beforeEach(() => {
            M.cfg.jsrev = 12345;
        });

        it('stores currentlogin on first use', () => {
            M.cfg.currentlogin = 1000;
            const storage = createMockStorage();
            new Storage(storage);

            const loginKey = `${Storage.hashString(M.cfg.wwwroot)}/currentlogin`;
            expect(storage.getItem(loginKey)).toBe('1000');
        });

        it('clears storage when currentlogin changes', () => {
            M.cfg.currentlogin = 1000;
            const storage = createMockStorage();

            const wrapper1 = new Storage(storage);
            wrapper1.set('mydata', 'value');
            expect(wrapper1.get('mydata')).toBe('value');

            // User re-logs in (new login timestamp).
            M.cfg.currentlogin = 2000;
            const wrapper2 = new Storage(storage);
            // Old data should be gone.
            expect(wrapper2.get('mydata')).toBeNull();
        });

        it('preserves storage when currentlogin is unchanged', () => {
            M.cfg.currentlogin = 1000;
            const storage = createMockStorage();

            const wrapper1 = new Storage(storage);
            wrapper1.set('mydata', 'value');

            // Same login, same jsrev.
            const wrapper2 = new Storage(storage);
            expect(wrapper2.get('mydata')).toBe('value');
        });

        it('does not write login key when currentlogin is null (guest)', () => {
            M.cfg.currentlogin = null;
            const storage = createMockStorage();
            new Storage(storage);

            const loginKey = `${Storage.hashString(M.cfg.wwwroot)}/currentlogin`;
            expect(storage.getItem(loginKey)).toBeNull();
        });

        it('clears storage when both jsrev and currentlogin change', () => {
            M.cfg.currentlogin = 1000;
            const storage = createMockStorage();

            const wrapper1 = new Storage(storage);
            wrapper1.set('mydata', 'value');

            M.cfg.jsrev = 99999;
            M.cfg.currentlogin = 2000;
            const wrapper2 = new Storage(storage);
            expect(wrapper2.get('mydata')).toBeNull();
        });

        it('re-writes jsrev sentinel after login-triggered clear', () => {
            M.cfg.currentlogin = 1000;
            const storage = createMockStorage();
            new Storage(storage);

            // Change login only (not jsrev).
            M.cfg.currentlogin = 2000;
            new Storage(storage);

            // The jsrev sentinel should still be present after the clear.
            const jsrevKey = `${Storage.hashString(M.cfg.wwwroot)}/jsrev`;
            expect(storage.getItem(jsrevKey)).toBe('12345');
        });
    });

    describe('when storage throws on setItem', () => {
        beforeEach(() => {
            M.cfg.jsrev = 12345;
        });

        afterEach(() => {
            jest.restoreAllMocks();
        });

        it('set returns false when storage is full', () => {
            const mockStorage = createMockStorage();
            const wrapper = new Storage(mockStorage);

            jest.spyOn(mockStorage, 'setItem').mockImplementation(() => {
                throw new DOMException('QuotaExceededError');
            });
            expect(wrapper.set('key', 'value')).toBe(false);
        });
    });
});
