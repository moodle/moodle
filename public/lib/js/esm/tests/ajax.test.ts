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
 * Tests for the core/ajax ESM module.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {performFetch, fetchOne, fetchMany, isMoodleAjaxError} from '@moodle/lms/core/ajax';
import type {AjaxRequest} from '@moodle/lms/core/ajax';
import {redirect} from '@moodle/lms/core/location';
import getGlobalAbortSignal, {abortGlobalFetches, resetGlobalAbortController} from '../src/abort';

// The global M.cfg is set up by .jest/globalM.ts with:
//   wwwroot: 'https://example.com'
//   sesskey: 'test-sesskey'
//   traceId: 'test-trace-id'

describe('core/ajax', () => {
    let fetchMock: jest.Mock;

    beforeEach(() => {
        // We are testing the actual performFetch implementation, so restore it if it was mocked in globalSetup.
        (performFetch as jest.Mock).mockRestore(); // Restore original implementation for each test.
        (fetchMany as jest.Mock).mockRestore();
        (fetchOne as jest.Mock).mockRestore();

        // Mock the underlying fetch function to control AJAX responses in tests.
        fetchMock = jest.fn();
        globalThis.fetch = fetchMock;
    });

    afterEach(() => {
        delete (globalThis as any).fetch;
    });

    /** Helper to create a successful fetch Response. */
    function mockFetchResponse(body: unknown, status = 200): Response {
        return {
            ok: status >= 200 && status < 300,
            status,
            statusText: status === 200 ? 'OK' : 'Error',
            json: () => Promise.resolve(body),
        } as Response;
    }

    describe('isMoodleAjaxError', () => {
        it('returns true for objects with message and errorcode', () => {
            expect(isMoodleAjaxError({message: 'fail', errorcode: 'invalidrecord'})).toBe(true);
        });

        it('returns false for plain errors', () => {
            expect(isMoodleAjaxError(new Error('oops'))).toBe(false);
        });

        it('returns false for null/undefined', () => {
            expect(isMoodleAjaxError(null)).toBe(false);
            expect(isMoodleAjaxError(undefined)).toBe(false);
        });
    });

    describe('performFetch', () => {
        it('sends a POST to service.php with sesskey for loginrequired=true', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: {id: 1}},
            ]));

            const requests: AjaxRequest[] = [{methodname: 'core_get_thing', args: {id: 1}}];
            const [promise] = performFetch(requests);
            const result = await promise;

            expect(result).toEqual({id: 1});
            expect(fetchMock).toHaveBeenCalledTimes(1);

            const [url, init] = fetchMock.mock.calls[0];
            expect(url).toContain('/lib/ajax/service.php');
            expect(url).toContain('sesskey=test-sesskey');
            expect(url).toContain('info=core_get_thing');
            expect(init.method).toBe('POST');
            expect(init.credentials).toBe('same-origin');
            expect(JSON.parse(init.body)).toEqual([
                {index: 0, methodname: 'core_get_thing', args: {id: 1}},
            ]);
        });

        it('sends to service-nologin.php when loginrequired=false', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: 'hello'},
            ]));

            const requests: AjaxRequest[] = [{methodname: 'core_get_string', args: {}}];
            const [promise] = performFetch(requests, {loginrequired: false});
            await promise;

            const [url] = fetchMock.mock.calls[0];
            expect(url).toContain('/lib/ajax/service-nologin.php');
            expect(url).not.toContain('sesskey=');
        });

        it('uses GET with cachekey for nologin requests', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: 'cached'},
            ]));

            const requests: AjaxRequest[] = [{methodname: 'core_get_string', args: {}}];
            const [promise] = performFetch(requests, {loginrequired: false, cachekey: 12345});
            await promise;

            const [url, init] = fetchMock.mock.calls[0];
            expect(url).toContain('cachekey=12345');
            expect(init.method).toBe('GET');
            expect(url).toContain('args=');
            expect(init.body).toBeUndefined();
        });

        it('falls back to POST when GET URL exceeds 2000 chars', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: 'ok'},
            ]));

            // Create args large enough to exceed 2000 chars when encoded.
            const bigArgs = {data: 'x'.repeat(2000)};
            const requests: AjaxRequest[] = [{methodname: 'core_get_big', args: bigArgs}];
            const [promise] = performFetch(requests, {loginrequired: false, cachekey: 1});
            await promise;

            const [, init] = fetchMock.mock.calls[0];
            expect(init.method).toBe('POST');
            expect(init.body).toBeDefined();
        });

        it('appends nosessionupdate=true when option is set', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: null},
            ]));

            const requests: AjaxRequest[] = [{methodname: 'core_ping', args: {}}];
            const [promise] = performFetch(requests, {nosessionupdate: true});
            await promise;

            const [url] = fetchMock.mock.calls[0];
            expect(url).toContain('nosessionupdate=true');
        });

        it('batches multiple requests in one HTTP call', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: 'a'},
                {error: false, data: 'b'},
                {error: false, data: 'c'},
            ]));

            const requests: AjaxRequest[] = [
                {methodname: 'ws_a', args: {}},
                {methodname: 'ws_b', args: {}},
                {methodname: 'ws_c', args: {}},
            ];
            const promises = performFetch(requests);

            expect(promises).toHaveLength(3);
            const results = await Promise.all(promises);
            expect(results).toEqual(['a', 'b', 'c']);
            expect(fetchMock).toHaveBeenCalledTimes(1);
        });

        it('sorts method names in info param when <= 5 methods', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: 1},
                {error: false, data: 2},
            ]));

            const requests: AjaxRequest[] = [
                {methodname: 'z_method', args: {}},
                {methodname: 'a_method', args: {}},
            ];
            performFetch(requests);

            const [url] = fetchMock.mock.calls[0];
            expect(url).toContain('info=a_method,z_method');
        });

        it('uses count summary when > 5 methods', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse(
                Array.from({length: 6}, () => ({error: false, data: null}))
            ));

            const requests: AjaxRequest[] = Array.from({length: 6}, (_, i) => ({
                methodname: `method_${i}`,
                args: {},
            }));
            performFetch(requests);

            const [url] = fetchMock.mock.calls[0];
            expect(url).toContain('info=6-method-calls');
        });

        it('sets pageparent header with traceId', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: null},
            ]));

            performFetch([{methodname: 'test', args: {}}]);

            const [, init] = fetchMock.mock.calls[0];
            expect(init.headers.pageparent).toBe('test-trace-id');
        });

        it('rejects individual promises on per-request errors', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: 'ok'},
                {error: true, exception: {message: 'bad', errorcode: 'invalidparam'}},
            ]));

            const requests: AjaxRequest[] = [
                {methodname: 'ws_ok', args: {}},
                {methodname: 'ws_bad', args: {}},
            ];
            const [p1, p2] = performFetch(requests);

            // First request succeeds before the error.
            await expect(p1).resolves.toBe('ok');
            // Both remaining get rejected (including first, but it's already settled).
            await expect(p2).rejects.toEqual({message: 'bad', errorcode: 'invalidparam'});
        });

        it('rejects all on batch-level error', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse({
                error: true,
                exception: {message: 'batch fail', errorcode: 'serverdown'},
            }));

            const [p1, p2] = performFetch([
                {methodname: 'a', args: {}},
                {methodname: 'b', args: {}},
            ]);

            await expect(p1).rejects.toMatchObject({error: true});
            await expect(p2).rejects.toMatchObject({error: true});
        });

        it('rejects all on HTTP error', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse(null, 500));

            const [promise] = performFetch([{methodname: 'test', args: {}}]);
            await expect(promise).rejects.toThrow('HTTP 500');
        });

        it('rejects all on network error', async() => {
            fetchMock.mockRejectedValue(new TypeError('Failed to fetch'));

            const [promise] = performFetch([{methodname: 'test', args: {}}]);
            await expect(promise).rejects.toBeInstanceOf(TypeError);
        });

        describe('globalAbortController', () => {
            beforeEach(() => {
                resetGlobalAbortController();
            });

            afterEach(() => {
                resetGlobalAbortController();
            });

            it('passes the globalAbortController signal to fetch calls', async() => {
                fetchMock.mockResolvedValue(mockFetchResponse([
                    {error: false, data: null},
                ]));

                performFetch([{methodname: 'test', args: {}}]);

                const [, init] = fetchMock.mock.calls[0];
                expect(init.signal).toBeDefined();
                expect(init.signal).toBe(getGlobalAbortSignal());
            });

            it('aborts all in-flight requests when globalAbortController is aborted', async() => {
                const abortError = new DOMException('The operation was aborted.', 'AbortError');
                fetchMock.mockImplementation((_url: string, init: RequestInit) => {
                    return new Promise((resolve, reject) => {
                        init.signal?.addEventListener('abort', () => reject(abortError));
                    });
                });

                const [promise] = performFetch([{methodname: 'test', args: {}}]);

                abortGlobalFetches();

                await expect(promise).rejects.toThrow('aborted');
            });
        });

        describe('timeout handling', () => {
            beforeEach(() => {
                jest.useFakeTimers();
                resetGlobalAbortController();
            });

            afterEach(() => {
                jest.useRealTimers();
                resetGlobalAbortController();
            });

            it('aborts the request after timeout', async() => {
                const abortError = new DOMException('The operation was aborted.', 'AbortError');
                fetchMock.mockImplementation((_url: string, init: RequestInit) => {
                    return new Promise((resolve, reject) => {
                        init.signal?.addEventListener('abort', () => reject(abortError));
                    });
                });

                const [promise] = performFetch(
                    [{methodname: 'slow', args: {}}],
                    {timeout: 5000},
                );

                jest.advanceTimersByTime(5000);

                await expect(promise).rejects.toThrow('aborted');
            });

            it('aborts a timed request when globalAbortController is aborted before the timeout fires', async() => {
                const abortError = new DOMException('The operation was aborted.', 'AbortError');
                fetchMock.mockImplementation((_url: string, init: RequestInit) => {
                    return new Promise((resolve, reject) => {
                        init.signal?.addEventListener('abort', () => reject(abortError));
                    });
                });

                const [promise] = performFetch(
                    [{methodname: 'slow', args: {}}],
                    {timeout: 5000},
                );

                // Abort the global controller before the timeout fires.
                abortGlobalFetches();

                await expect(promise).rejects.toThrow('aborted');
            });
        });

        it('redirects to login on servicerequireslogin error', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: true, exception: {message: 'login required', errorcode: 'servicerequireslogin'}},
            ]));

            expectRedirect({urlContains: '/login/index.php'});

            const [promise] = performFetch([{methodname: 'test', args: {}}]);

            // Wait for the async chain to complete.
            await new Promise(process.nextTick);
        });

        it('does NOT redirect on servicerequireslogin when nosessionupdate=true', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: true, exception: {message: 'login required', errorcode: 'servicerequireslogin'}},
            ]));

            const [promise] = performFetch(
                [{methodname: 'test', args: {}}],
                {nosessionupdate: true},
            );

            // With nosessionupdate=true, the error should be rejected normally (no redirect).
            await expect(promise).rejects.toMatchObject({
                errorcode: 'servicerequireslogin',
            });
            expect(redirect).not.toHaveBeenCalled();
        });
    });

    describe('fetchOne', () => {
        it('returns a single typed promise', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: {name: 'Test'}},
            ]));

            const result = await fetchOne<{ name: string }>({
                methodname: 'core_get_user',
                args: {id: 1},
            });

            expect(result).toEqual({name: 'Test'});
        });
    });

    describe('fetchMany', () => {
        it('returns all results as an array', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: 'first'},
                {error: false, data: 'second'},
            ]));

            const results = await fetchMany<string>([
                {methodname: 'ws_a', args: {}},
                {methodname: 'ws_b', args: {}},
            ]);

            expect(results).toEqual(['first', 'second']);
        });

        it('rejects if any request fails', async() => {
            fetchMock.mockResolvedValue(mockFetchResponse([
                {error: false, data: 'ok'},
                {error: true, exception: {message: 'nope', errorcode: 'err'}},
            ]));

            await expect(
                fetchMany([
                    {methodname: 'ws_a', args: {}},
                    {methodname: 'ws_b', args: {}},
                ])
            ).rejects.toMatchObject({message: 'nope', errorcode: 'err'});
        });
    });
});
