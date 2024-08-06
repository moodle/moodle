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
    signal: AbortSignal | null;

    constructor(input: string | URL, init: RequestInit = {}) {
        this.url = String(input);
        this.method = init.method ?? 'GET';
        this.headers = new Headers(init.headers as Record<string, string>);
        this.body = init.body ?? null;
        this.signal = (init.signal as AbortSignal | null | undefined) ?? null;
    }

    async text(): Promise<string> {
        return this.body ?? '';
    }
}

class MockResponse {
    ok: boolean;
    status: number;
    statusText: string;
    headers: Headers;
    body: any;

    constructor(body?: any, init: {status?: number; statusText?: string; headers?: Record<string, string>} = {}) {
        this.status = init.status ?? 200;
        this.statusText = init.statusText ?? 'OK';
        this.ok = this.status >= 200 && this.status < 300;
        this.headers = new Headers(init.headers);
        this.body = body ?? null;
    }

    async text(): Promise<string> {
        return this.body ?? '';
    }

    async json(): Promise<any> {
        return typeof this.body === 'string' ? JSON.parse(this.body) : this.body;
    }
}

(globalThis as any).Request = MockRequest;
(globalThis as any).Response = MockResponse;

const mockFetch = jest.fn();
(globalThis as any).fetch = mockFetch;

import Fetch from '@moodle/lms/core/fetch';
import getGlobalAbortSignal, {abortGlobalFetches, resetGlobalAbortController} from '../src/abort';

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

        it('sends an empty pageparent header when telemetry is not set up', async() => {
            const {traceId} = (globalThis as any).M.cfg;
            (globalThis as any).M.cfg.traceId = null;

            try {
                await Fetch.request('mod_example', 'get_items');
            } finally {
                (globalThis as any).M.cfg.traceId = traceId;
            }

            const req = mockFetch.mock.calls[0][0];
            expect(req.headers.get('pageparent')).toBe('');
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

    describe('globalAbortController', () => {
        beforeEach(() => {
            resetGlobalAbortController();
        });

        afterEach(() => {
            resetGlobalAbortController();
        });

        it('passes the globalAbortController signal to fetch calls', async() => {
            await Fetch.request('mod_example', 'get_items');

            const req: MockRequest = mockFetch.mock.calls[0][0];
            expect(req.signal).toBeDefined();
            expect(req.signal).toBe(getGlobalAbortSignal());
        });

        it('aborts all in-flight requests when globalAbortController is aborted', async() => {
            const abortError = new DOMException('The operation was aborted.', 'AbortError');
            mockFetch.mockImplementation((req: MockRequest) => {
                return new Promise((resolve, reject) => {
                    req.signal?.addEventListener('abort', () => reject(abortError));
                });
            });

            const requestPromise = Fetch.request('mod_example', 'get_items');

            abortGlobalFetches();

            await expect(requestPromise).rejects.toThrow('aborted');
        });
    });

    // ── batching (instance methods / execute) ───────────────────────────────

    describe('batching', () => {
        /**
         * Build a multipart/mixed ODATA $batch response body from a list of per-request results,
         * in the same wire format produced by Fetch's own batch request builder.
         */
        const buildBatchResponseBody = (
            boundary: string,
            parts: Array<{
                id: string;
                status?: number;
                statusText?: string;
                contentType?: string;
                headers?: string[];
                body?: string;
            }>,
        ): string => {
            const partsText = parts.map(({
                id,
                status = 200,
                statusText = 'OK',
                contentType = 'application/json',
                headers = [],
                body,
            }) => {
                const lines = [
                    `--${boundary}`,
                    'Content-Type: application/http',
                    '',
                    `HTTP/1.1 ${status} ${statusText}`,
                    `Content-Type: ${contentType}`,
                    `Content-ID: ${id}`,
                    ...headers,
                    '',
                ];
                if (body) {
                    lines.push(body);
                }
                lines.push('');
                return lines.join('\r\n');
            });

            return [...partsText, `--${boundary}--`, ''].join('');
        };

        /** Extract the Content-ID values, in order, from an outgoing $batch request body. */
        const getRequestIds = (body: string): string[] =>
            [...body.matchAll(/^Content-ID: (.+)$/gm)].map(([, id]) => id);

        afterEach(() => {
            (globalThis as any).M.cfg.batchFetchRequests = false;
        });

        it('executes a single queued request directly, without using the batch endpoint', async() => {
            const batcher = new Fetch();
            const resultPromise = batcher.performGet('mod_example', 'list');
            await batcher.execute();
            await resultPromise;

            expect(mockFetch).toHaveBeenCalledTimes(1);
            const req = mockFetch.mock.calls[0][0];
            expect(req.url).toBe('https://example.com/rest/v2/mod_example/list');
            expect(req.method).toBe('GET');
        });

        it('executes immediately when batchFetchRequests is disabled', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = false;
            const batcher = new Fetch();

            await batcher.performGet('mod_example', 'list');

            expect(mockFetch).toHaveBeenCalledTimes(1);
            const req = mockFetch.mock.calls[0][0];
            expect(req.url).toBe('https://example.com/rest/v2/mod_example/list');
        });

        it('batches requests separately by Authorization header and includes the header in each sub-request', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = true;
            const batcher = new Fetch();
            const requests = [
                batcher.performGet('mod_example', 'cookie-one'),
                batcher.performGet('mod_example', 'cookie-two'),
                batcher.performGet('mod_example', 'token-one-a', {headers: {Authorization: 'Bearer token-one'}}),
                batcher.performGet('mod_example', 'token-one-b', {headers: {authorization: 'Bearer token-one'}}),
                batcher.performGet('mod_example', 'token-two-a', {headers: {Authorization: 'Bearer token-two'}}),
                batcher.performGet('mod_example', 'token-two-b', {headers: {Authorization: 'Bearer token-two'}}),
            ];

            mockFetch.mockImplementation(async(req: MockRequest) => {
                const ids = getRequestIds(req.body);
                return new MockResponse(
                    buildBatchResponseBody(
                        'resp-boundary',
                        ids.map((id) => ({id, body: JSON.stringify({})})),
                    ),
                    {headers: {'Content-Type': 'multipart/mixed;boundary=resp-boundary'}},
                );
            });

            await batcher.execute();
            await Promise.all(requests);

            expect(mockFetch).toHaveBeenCalledTimes(3);
            const authHeadersByBatch = await Promise.all(mockFetch.mock.calls.map(async([req]) => {
                const body = await req.text();
                return [...body.matchAll(/^authorization: (.+)$/gim)].map(([, value]) => value);
            }));
            expect(authHeadersByBatch).toEqual([
                [],
                ['Bearer token-one', 'Bearer token-one'],
                ['Bearer token-two', 'Bearer token-two'],
            ]);
        });

        it('queues requests until execute() is called when batchFetchRequests is enabled', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = true;
            const batcher = new Fetch();

            const first = batcher.performGet('mod_example', 'list');
            const second = batcher.performPost('mod_example', 'create', {body: {name: 'new'}});

            // Nothing has been sent yet.
            expect(mockFetch).not.toHaveBeenCalled();

            mockFetch.mockImplementation(async(req: MockRequest) => {
                const ids = getRequestIds(req.body);
                return new MockResponse(
                    buildBatchResponseBody('resp-boundary', [
                        {id: ids[0], body: JSON.stringify({items: []})},
                        {id: ids[1], status: 201, statusText: 'Created', body: JSON.stringify({id: 1})},
                    ]),
                    {headers: {'Content-Type': 'multipart/mixed;boundary=resp-boundary'}},
                );
            });

            await batcher.execute();

            expect(mockFetch).toHaveBeenCalledTimes(1);
            const batchRequest = mockFetch.mock.calls[0][0];
            expect(batchRequest.method).toBe('POST');
            expect(batchRequest.url).toBe('https://example.com/$batch');
            expect(batchRequest.headers.get('Content-Type')).toMatch(/^multipart\/mixed;boundary=/);

            const firstResponse = await first;
            expect(await firstResponse.json()).toEqual({items: []});

            const secondResponse = await second;
            expect(secondResponse.status).toBe(201);
            expect(await secondResponse.json()).toEqual({id: 1});
        });

        it('rejects all queued requests when the batch response is not ok', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = true;
            const batcher = new Fetch();

            const first = batcher.performGet('mod_example', 'list');
            const second = batcher.performGet('mod_example', 'other');

            mockFetch.mockResolvedValue(errorResponse(500, 'Internal Server Error'));

            await batcher.execute();

            await expect(first).rejects.toBe('Internal Server Error');
            await expect(second).rejects.toBe('Internal Server Error');
        });

        it('includes a debugging prologue line for each queued request in the batch body', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = true;
            const batcher = new Fetch();

            batcher.performGet('mod_example', 'list');
            batcher.performPost('mod_example', 'create', {body: {name: 'new'}});

            mockFetch.mockImplementation(async(req: MockRequest) => {
                const ids = getRequestIds(req.body);
                return new MockResponse(
                    buildBatchResponseBody('resp-boundary', [
                        {id: ids[0], body: JSON.stringify({items: []})},
                        {id: ids[1], status: 201, body: JSON.stringify({id: 1})},
                    ]),
                    {headers: {'Content-Type': 'multipart/mixed;boundary=resp-boundary'}},
                );
            });

            await batcher.execute();

            const batchRequest = mockFetch.mock.calls[0][0];
            const requestBody = await batchRequest.text();
            expect(requestBody).toMatch(
                /# Requesting GET https:\/\/example\.com\/rest\/v2\/mod_example\/list\r\n/,
            );
            expect(requestBody).toMatch(
                /# Requesting POST https:\/\/example\.com\/rest\/v2\/mod_example\/create\r\n/,
            );
        });

        it('correctly parses a sub-response status line with a multi-word status text', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = true;
            const batcher = new Fetch();

            const first = batcher.performGet('mod_example', 'list');
            const second = batcher.performGet('mod_example', 'other');

            mockFetch.mockImplementation(async(req: MockRequest) => {
                const ids = getRequestIds(req.body);
                return new MockResponse(
                    buildBatchResponseBody('resp-boundary', [
                        {id: ids[0], status: 404, statusText: 'Not Found', body: JSON.stringify({error: 'missing'})},
                        {id: ids[1], body: JSON.stringify({ok: true})},
                    ]),
                    {headers: {'Content-Type': 'multipart/mixed;boundary=resp-boundary'}},
                );
            });

            await batcher.execute();

            await expect(first).rejects.toBe('Not Found');

            const secondResponse = await second;
            expect(secondResponse.status).toBe(200);
        });

        it('preserves colons in sub-response header values', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = true;
            const batcher = new Fetch();

            const first = batcher.performGet('mod_example', 'list');
            const second = batcher.performGet('mod_example', 'other');

            mockFetch.mockImplementation(async(req: MockRequest) => {
                const ids = getRequestIds(req.body);
                return new MockResponse(
                    buildBatchResponseBody('resp-boundary', [
                        {
                            id: ids[0],
                            headers: ['Date: Sun, 17 Jan 2027 08:41:12 GMT'],
                            body: JSON.stringify({ok: true}),
                        },
                        {id: ids[1], body: JSON.stringify({ok: true})},
                    ]),
                    {headers: {'Content-Type': 'multipart/mixed;boundary=resp-boundary'}},
                );
            });

            await batcher.execute();

            const firstResponse = await first;
            expect(firstResponse.headers.get('Date')).toBe('Sun, 17 Jan 2027 08:41:12 GMT');
            await second;
        });

        it('flushes the queue when it reaches 20 requests, so no batch exceeds 20', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = true;
            const batcher = new Fetch();

            mockFetch.mockImplementation(async(req: MockRequest) => {
                // A lone queued request is sent directly rather than as a batch.
                if (req.method === 'GET') {
                    return new MockResponse(JSON.stringify({ok: true}));
                }
                const ids = getRequestIds(req.body);
                return new MockResponse(
                    buildBatchResponseBody('resp-boundary', ids.map((id) => ({id, body: JSON.stringify({ok: true})}))),
                    {headers: {'Content-Type': 'multipart/mixed;boundary=resp-boundary'}},
                );
            });

            const responses = Array.from({length: 21}, (_, i) => batcher.performGet('mod_example', `item${i}`));
            await batcher.execute();
            await Promise.all(responses);

            expect(mockFetch).toHaveBeenCalledTimes(2);
            // The 21st request is sent on its own, not appended to the first batch.
            const calls = mockFetch.mock.calls.map(([req]) => req);
            const batch = calls.find((req) => req.method === 'POST');
            expect(getRequestIds(batch.body)).toHaveLength(20);
            expect(calls.filter((req) => req.method === 'GET')).toHaveLength(1);
        });

        it('rejects a request with no matching Content-ID in the batch response', async() => {
            (globalThis as any).M.cfg.batchFetchRequests = true;
            const batcher = new Fetch();

            const first = batcher.performGet('mod_example', 'list');
            const second = batcher.performGet('mod_example', 'other');

            mockFetch.mockImplementation(async(req: MockRequest) => {
                const ids = getRequestIds(req.body);
                // Only respond to the first request.
                return new MockResponse(
                    buildBatchResponseBody('resp-boundary', [
                        {id: ids[0], body: JSON.stringify({ok: true})},
                    ]),
                    {headers: {'Content-Type': 'multipart/mixed;boundary=resp-boundary'}},
                );
            });

            await batcher.execute();

            await expect(first).resolves.toBeDefined();
            await expect(second).rejects.toMatch('No response provided for request');
        });

        it('does nothing when execute() is called with no queued requests', async() => {
            const batcher = new Fetch();

            await expect(batcher.execute()).resolves.toBeUndefined();
            expect(mockFetch).not.toHaveBeenCalled();
        });

        describe('getBatcher', () => {
            afterEach(() => {
                jest.useRealTimers();
            });

            it('returns the same singleton instance on repeated calls', () => {
                expect(Fetch.getBatcher()).toBe(Fetch.getBatcher());
            });

            it('automatically executes the batch after the auto-batch timeout', async() => {
                jest.useFakeTimers();
                (globalThis as any).M.cfg.batchFetchRequests = true;

                // Use a dedicated instance (rather than the shared getBatcher() singleton) configured
                // with the same 50ms auto-batch timeout, so this test is isolated from other tests.
                const batcher = new Fetch(50);
                batcher.performGet('mod_example', 'list');

                expect(mockFetch).not.toHaveBeenCalled();

                jest.advanceTimersByTime(50);
                // Allow any queued microtasks from execute() to flush.
                await Promise.resolve();
                await Promise.resolve();

                expect(mockFetch).toHaveBeenCalledTimes(1);
            });
        });
    });
});
