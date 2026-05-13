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
 * Tests for the core/fetch ESM module.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// jsdom does not provide Request or Response — supply minimal polyfills.
class MockRequest {
    url: string;
    method: string;
    headers: Headers;
    body: any;

    constructor(input: string | URL, init: RequestInit = {}) {
        this.url = String(input);
        this.method = init.method ?? 'GET';
        this.headers = new Headers(init.headers as Record<string, string>);
        this.body = init.body ?? null;
    }
}

class MockResponse {
    ok: boolean;
    status: number;
    statusText: string;
    headers: Headers;

    constructor(body?: any, init: {status?: number; statusText?: string; headers?: Record<string, string>} = {}) {
        this.status = init.status ?? 200;
        this.statusText = init.statusText ?? 'OK';
        this.ok = this.status >= 200 && this.status < 300;
        this.headers = new Headers(init.headers);
    }
}

(globalThis as any).Request = MockRequest;
(globalThis as any).Response = MockResponse;

const mockFetch = jest.fn();
(globalThis as any).fetch = mockFetch;

import Fetch from '@moodle/lms/core/fetch';

/**
 * Helper: create a successful mock response.
 */
const okResponse = (init: {statusText?: string; headers?: Record<string, string>} = {}): MockResponse =>
    new MockResponse(null, {status: 200, statusText: 'OK', ...init});

/**
 * Helper: create a failed mock response.
 */
const errorResponse = (status = 500, statusText = 'Internal Server Error'): MockResponse =>
    new MockResponse(null, {status, statusText});

describe('@moodle/lms/core/fetch', () => {
    beforeEach(() => {
        mockFetch.mockReset();
        mockFetch.mockResolvedValue(okResponse());
    });

    // ── request() ─────────────────────────────────────────────────────────

    describe('request', () => {
        it('sends a GET request to the correct URL', async() => {
            await Fetch.request('mod_example', 'get_items');

            expect(mockFetch).toHaveBeenCalledTimes(1);
            const req = mockFetch.mock.calls[0][0];
            expect(req.url).toBe('https://example.com/rest/v2/mod_example/get_items');
            expect(req.method).toBe('GET');
        });

        it('strips core_ prefix from component', async() => {
            await Fetch.request('core_course', 'list');

            const req = mockFetch.mock.calls[0][0];
            expect(req.url).toBe('https://example.com/rest/v2/course/list');
        });

        it('appends query params', async() => {
            await Fetch.request('mod_example', 'search', {params: {q: 'hello', limit: '10'}});

            const req = mockFetch.mock.calls[0][0];
            const url = new URL(req.url);
            expect(url.searchParams.get('q')).toBe('hello');
            expect(url.searchParams.get('limit')).toBe('10');
        });

        it('sends JSON body for object bodies', async() => {
            await Fetch.request('mod_example', 'create', {
                method: 'POST',
                body: {name: 'test'},
            });

            const req = mockFetch.mock.calls[0][0];
            expect(req.method).toBe('POST');
            expect(req.body).toBe(JSON.stringify({name: 'test'}));
        });

        it('sends string body as-is', async() => {
            await Fetch.request('mod_example', 'create', {
                method: 'POST',
                body: 'raw-data',
            });

            const req = mockFetch.mock.calls[0][0];
            expect(req.body).toBe('raw-data');
        });

        it('sends FormData body as-is', async() => {
            const formData = new FormData();
            formData.append('file', 'content');

            await Fetch.request('mod_example', 'upload', {
                method: 'POST',
                body: formData,
            });

            const req = mockFetch.mock.calls[0][0];
            expect(req.body).toBe(formData);
        });

        it('includes default headers', async() => {
            await Fetch.request('mod_example', 'get_items');

            const req = mockFetch.mock.calls[0][0];
            expect(req.headers.get('Accept')).toBe('application/json');
            expect(req.headers.get('Content-Type')).toBe('application/json');
            expect(req.headers.get('pageparent')).toBe('test-trace-id');
        });

        it('merges custom headers with defaults', async() => {
            await Fetch.request('mod_example', 'get_items', {
                headers: {'X-Custom': 'value'},
            });

            const req = mockFetch.mock.calls[0][0];
            expect(req.headers.get('X-Custom')).toBe('value');
            expect(req.headers.get('Accept')).toBe('application/json');
        });

        it('includes cachekey in URL when > 1', async() => {
            await Fetch.request('mod_example', 'get_items', {cachekey: 42});

            const req = mockFetch.mock.calls[0][0];
            expect(req.url).toContain('cachekey:42');
            expect(req.url).toBe('https://example.com/rest/v2/cachekey:42/mod_example/get_items');
        });

        it('omits cachekey when null', async() => {
            await Fetch.request('mod_example', 'get_items', {cachekey: null});

            const req = mockFetch.mock.calls[0][0];
            expect(req.url).not.toContain('cachekey');
        });

        it('omits cachekey when <= 1', async() => {
            await Fetch.request('mod_example', 'get_items', {cachekey: 1});

            const req = mockFetch.mock.calls[0][0];
            expect(req.url).not.toContain('cachekey');
        });

        it('resolves with the response on success', async() => {
            const expected = okResponse();
            mockFetch.mockResolvedValue(expected);

            const response = await Fetch.request('mod_example', 'get_items');
            expect(response).toBe(expected);
        });

        it('rejects with statusText on error response', async() => {
            mockFetch.mockResolvedValue(errorResponse(404, 'Not Found'));

            await expect(Fetch.request('mod_example', 'missing')).rejects.toBe('Not Found');
        });

        it('registers and completes a Pending operation', async() => {
            await Fetch.request('mod_example', 'get_items');

            expect(pendingStack).toContain('Requesting mod_example/get_items with GET');
            expect(completeStack).toContain('Requesting mod_example/get_items with GET');
        });
    });

    // ── performGet ────────────────────────────────────────────────────────

    describe('performGet', () => {
        it('sends a GET request', async() => {
            await Fetch.performGet('mod_example', 'list');

            const req = mockFetch.mock.calls[0][0];
            expect(req.method).toBe('GET');
            expect(req.url).toBe('https://example.com/rest/v2/mod_example/list');
        });

        it('forwards params', async() => {
            await Fetch.performGet('mod_example', 'list', {params: {page: '2'}});

            const url = new URL(mockFetch.mock.calls[0][0].url);
            expect(url.searchParams.get('page')).toBe('2');
        });

        it('forwards cachekey', async() => {
            await Fetch.performGet('mod_example', 'list', {cachekey: 99});

            const req = mockFetch.mock.calls[0][0];
            expect(req.url).toContain('cachekey:99');
        });

        it('forwards custom headers', async() => {
            await Fetch.performGet('mod_example', 'list', {headers: {'X-Test': 'yes'}});

            const req = mockFetch.mock.calls[0][0];
            expect(req.headers.get('X-Test')).toBe('yes');
        });
    });

    // ── performHead ───────────────────────────────────────────────────────

    describe('performHead', () => {
        it('sends a HEAD request', async() => {
            await Fetch.performHead('mod_example', 'check');

            const req = mockFetch.mock.calls[0][0];
            expect(req.method).toBe('HEAD');
        });
    });

    // ── performPost ───────────────────────────────────────────────────────

    describe('performPost', () => {
        it('sends a POST request with body', async() => {
            await Fetch.performPost('mod_example', 'create', {body: {name: 'new'}});

            const req = mockFetch.mock.calls[0][0];
            expect(req.method).toBe('POST');
            expect(req.body).toBe(JSON.stringify({name: 'new'}));
        });
    });

    // ── performPut ────────────────────────────────────────────────────────

    describe('performPut', () => {
        it('sends a PUT request with body', async() => {
            await Fetch.performPut('mod_example', 'update', {body: {id: 1}});

            const req = mockFetch.mock.calls[0][0];
            expect(req.method).toBe('PUT');
            expect(req.body).toBe(JSON.stringify({id: 1}));
        });
    });

    // ── performPatch ──────────────────────────────────────────────────────

    describe('performPatch', () => {
        it('sends a PATCH request with body', async() => {
            await Fetch.performPatch('mod_example', 'partial', {body: {field: 'val'}});

            const req = mockFetch.mock.calls[0][0];
            expect(req.method).toBe('PATCH');
            expect(req.body).toBe(JSON.stringify({field: 'val'}));
        });
    });

    // ── performDelete ─────────────────────────────────────────────────────

    describe('performDelete', () => {
        it('sends a DELETE request', async() => {
            await Fetch.performDelete('mod_example', 'remove');

            const req = mockFetch.mock.calls[0][0];
            expect(req.method).toBe('DELETE');
        });

        it('forwards params', async() => {
            await Fetch.performDelete('mod_example', 'remove', {params: {id: '5'}});

            const url = new URL(mockFetch.mock.calls[0][0].url);
            expect(url.searchParams.get('id')).toBe('5');
        });

        it('forwards body', async() => {
            await Fetch.performDelete('mod_example', 'remove', {body: {reason: 'test'}});

            const req = mockFetch.mock.calls[0][0];
            expect(req.body).toBe(JSON.stringify({reason: 'test'}));
        });
    });
});
