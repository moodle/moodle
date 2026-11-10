var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * The core/fetch module allows you to make web service requests to the Moodle REST API.
 *
 * Most methods are available both statically and via an instance method, for example
 * `Fetch.performGet()` and `(new Fetch()).performGet()`.
 *
 * The static perform methods perform immediate individual requests, whilst instance
 * methods are useful for batching requests.
 *
 * By default a Fetch instance will not automatically execute the batch, but it can be configured to do so
 * by passing a value for the `autoBatchTimeout` parameter.
 *
 * The batcher can be executed manually by calling the {@link Fetch#execute} method.
 *
 * Note: In cases where the batcher is executed with a single request the batch endpoint is _not_ used.
 *
 * A helper method, {@link Fetch.getBatcher}, exists to fetch a singleton instance of the class.
 * This singleton is configured to automatically execute batch requests.
 *
 * @module     core/fetch
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example <caption>Perform a single GET request</caption>
 * import Fetch from 'core/fetch';
 *
 * const result = Fetch.performGet('mod_example', 'animals', { params: { type: 'mammal' } });
 *
 * result.then((response) => {
 *    // Do something with the Response object.
 * })
 * .catch((error) => {
 *     // Handle the error
 * });
 *
 * @example <caption>Perform a series of requests, automatically batching them</caption>
 * import Fetch from 'core/fetch';
 *
 * // Execute a single request containing three sub requests.
 * const batcher = Fetch.getBatcher();
 * const actions = await Promise.all([
 *     batcher.performGet('mod_example', 'animals', { params: { type: 'mammal' } }),
 *     batcher.performGet('mod_example', 'animals', { params: { type: 'reptile' } }),
 *     batcher.performDelete('mod_example', `animals/${pig.id}`),
 * ]);
 *
 * @example <caption>Perform a series of GET requests, manually batching them</caption>
 * import Fetch from 'core/fetch';
 *
 * const batcher = new Fetch();
 * const actions = Promise.all([
 *     batcher.performGet('mod_example', 'animals', { params: { type: 'mammal' } }),
 *     batcher.performGet('mod_example', 'animals', { params: { type: 'reptile' } }),
 *     batcher.performDelete('mod_example', `animals/${pig.id}`),
 * ]);
 *
 * batcher.execute();
 *
 * await actions;
 */
import { getGlobalAbortSignal } from "./abort";
import config from "@moodle/lms/core/config";
import Pending from "@moodle/lms/core/pending";
class RequestWrapper {
  static {
    __name(this, "RequestWrapper");
  }
  #request;
  #promise;
  #resolve;
  #reject;
  constructor(request) {
    this.#request = request;
    this.#promise = new Promise((resolve, reject) => {
      this.#resolve = resolve;
      this.#reject = reject;
    });
  }
  get request() {
    return this.#request;
  }
  get promise() {
    return this.#promise;
  }
  /** The Promise rejector, exposed so a batch response can reject individual sub-requests. */
  get reject() {
    return this.#reject;
  }
  handleResponse(response) {
    if (response.ok) {
      this.#resolve(response);
    } else {
      this.#reject(response.statusText);
    }
  }
}
class Fetch {
  static {
    __name(this, "Fetch");
  }
  /** The singleton instance of the automatic batcher, created lazily by {@link Fetch.getBatcher}. */
  static #batcher = null;
  /**
   * Get the singleton instance of the batch processor.
   *
   * Note: The singleton instance is configured to automatically execute the batch 50ms after the initial request in the group
   * is added.
   *
   * @returns The singleton {@link Fetch} batcher instance.
   */
  static getBatcher() {
    if (!this.#batcher) {
      this.#batcher = new this(50);
    }
    return this.#batcher;
  }
  /** Requests queued for the next batch execution, grouped by authentication context. */
  #requestMap = /* @__PURE__ */ new Map();
  /** The timer handle used to automatically execute the batch after {@link Fetch#autoBatchTimeout}. */
  #delayTimer = null;
  /** Whether requests should be queued and sent as a single batch, per the `batchFetchRequests` config value. */
  #batchRequests;
  /**
   * The delay, in milliseconds, to use for the automatic batch timer.
   * A `null` value disables automatic execution.
   */
  #autoBatchTimeout;
  /**
   * Make a single request to the Moodle API.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options Request options (params, body, method, headers, cachekey).
   * @returns A promise that resolves to the {@link Response}.
   */
  // eslint-disable-next-line unicorn/consistent-class-member-order -- Group singleton state first.
  static async request(component, action, {
    cachekey = null,
    headers = {},
    params = {},
    body = null,
    method = "GET"
  } = {}) {
    const resolvePending = new Pending(`Requesting ${component}/${action} with ${method}`);
    const requestWrapper = Fetch.#getRequest(
      Fetch.#normaliseComponent(component),
      action,
      {
        headers,
        params,
        method,
        body,
        cachekey
      }
    );
    const result = await fetch(requestWrapper.request);
    resolvePending.resolve();
    requestWrapper.handleResponse(result);
    return requestWrapper.promise;
  }
  /**
   * Perform a GET request.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options Optional query-string parameters.
   */
  static async performGet(component, action, { cachekey = null, headers = {}, params = {} } = {}) {
    return this.request(component, action, {
      cachekey,
      headers,
      params,
      method: "GET"
    });
  }
  /**
   * Perform a HEAD request.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options Optional query-string parameters.
   */
  static async performHead(component, action, { headers = {}, params = {} } = {}) {
    return this.request(component, action, { headers, params, method: "HEAD" });
  }
  /**
   * Perform a POST request.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options The request body and optional headers.
   */
  static async performPost(component, action, { headers = {}, body }) {
    return this.request(component, action, { headers, body, method: "POST" });
  }
  /**
   * Perform a PUT request.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options The request body and optional headers.
   */
  static async performPut(component, action, { headers = {}, body }) {
    return this.request(component, action, { headers, body, method: "PUT" });
  }
  /**
   * Perform a PATCH request.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options The request body and optional headers.
   */
  static async performPatch(component, action, { headers = {}, body }) {
    return this.request(component, action, { headers, body, method: "PATCH" });
  }
  /**
   * Perform a DELETE request.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options Optional query-string parameters and/or body.
   */
  static async performDelete(component, action, { headers = {}, params = {}, body = null } = {}) {
    return this.request(component, action, {
      headers,
      body,
      params,
      method: "DELETE"
    });
  }
  /**
   * Create a new instance of the Fetch class.
   *
   * When methods are called on an instance, they are queued and executed as a batch.
   *
   * By default a Fetch instance will not automatically execute the batch, but it can be configured to do so
   * by passing a value for the `autoBatchTimeout` parameter.
   *
   * The batcher can be executed manually by calling the {@link Fetch#execute} method.
   *
   * In cases where the batcher is executed with a single request the batch endpoint is _not_ used.
   *
   * @param autoBatchTimeout The amount of time, in milliseconds, to use for the automatic batch timer.
   * If null, the autobatcher is disabled.
   */
  constructor(autoBatchTimeout = null) {
    this.#autoBatchTimeout = autoBatchTimeout;
    this.#batchRequests = config.batchFetchRequests;
  }
  /**
   * Queue a GET request to be sent as part of a batch.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options Optional query-string parameters.
   * @returns A promise that resolves to the {@link Response} for this request within the batch.
   */
  async performGet(component, action, { cachekey = null, headers = {}, params = {} } = {}) {
    return this.#addRequest(component, action, {
      cachekey,
      headers,
      params,
      method: "GET"
    });
  }
  /**
   * Queue a HEAD request to be sent as part of a batch.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options Optional query-string parameters.
   * @returns A promise that resolves to the {@link Response} for this request within the batch.
   */
  async performHead(component, action, { headers = {}, params = {} } = {}) {
    return this.#addRequest(component, action, { headers, params, method: "HEAD" });
  }
  /**
   * Queue a POST request to be sent as part of a batch.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options The request body and optional headers.
   * @returns A promise that resolves to the {@link Response} for this request within the batch.
   */
  async performPost(component, action, { headers = {}, body }) {
    return this.#addRequest(component, action, { headers, body, method: "POST" });
  }
  /**
   * Queue a PUT request to be sent as part of a batch.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options The request body and optional headers.
   * @returns A promise that resolves to the {@link Response} for this request within the batch.
   */
  async performPut(component, action, { headers = {}, body }) {
    return this.#addRequest(component, action, { headers, body, method: "PUT" });
  }
  /**
   * Queue a PATCH request to be sent as part of a batch.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options The request body and optional headers.
   * @returns A promise that resolves to the {@link Response} for this request within the batch.
   */
  async performPatch(component, action, { headers = {}, body }) {
    return this.#addRequest(component, action, { headers, body, method: "PATCH" });
  }
  /**
   * Queue a DELETE request to be sent as part of a batch.
   *
   * @param component The frankenstyle component name.
   * @param action The component action to perform.
   * @param options Optional query-string parameters and/or body.
   * @returns A promise that resolves to the {@link Response} for this request within the batch.
   */
  async performDelete(component, action, { headers = {}, params = {}, body = null } = {}) {
    return this.#addRequest(component, action, {
      headers,
      body,
      params,
      method: "DELETE"
    });
  }
  /**
   * Execute the batch request.
   *
   * If only a single request has been queued, it is sent directly rather than wrapped in a batch request.
   */
  async execute() {
    if (this.#delayTimer !== null) {
      clearTimeout(this.#delayTimer);
      this.#delayTimer = null;
    }
    const requestMap = this.#requestMap;
    this.#requestMap = /* @__PURE__ */ new Map();
    if (requestMap.size === 0) {
      return;
    }
    let requestCount = 0;
    for (const group of requestMap.values()) {
      requestCount += group.size;
    }
    if (requestCount === 1) {
      const requestGroup = requestMap.values().next().value;
      await this.#executeRequestMap(requestGroup);
      return;
    }
    const resolvePending = new Pending(`core/fetch:execute:${requestCount}`);
    for (const group of requestMap.values()) {
      await this.#executeRequestMap(group);
    }
    resolvePending.resolve();
  }
  /**
   * Execute requests sharing the same explicit Authorization header.
   *
   * @param requestMap The map of request IDs to wrappers.
   */
  async #executeRequestMap(requestMap) {
    if (requestMap.size === 1) {
      const requestWrapper = requestMap.values().next().value;
      const result = await fetch(requestWrapper.request);
      requestWrapper.handleResponse(result);
      return;
    }
    const response = await fetch(await this.#getRequests(requestMap));
    if (!response.ok) {
      for (const requestWrapper of requestMap.values()) {
        requestWrapper.reject(response.statusText);
      }
      return;
    }
    const responses = await this.#processBatchResponse(response);
    const responseMap = /* @__PURE__ */ new Map();
    for (const responseData of responses) {
      const subResponse = this.#getResponseFromText(responseData);
      const id = subResponse.headers.get("Content-ID") ?? "";
      responseMap.set(id, subResponse);
    }
    for (const [key, requestWrapper] of requestMap) {
      if (responseMap.has(key)) {
        requestWrapper.handleResponse(responseMap.get(key));
      } else {
        requestWrapper.reject(`Request failed. No response provided for request ${key} by provider`);
      }
    }
  }
  /**
   * Normalise a component name by stripping the `core_` prefix.
   */
  static #normaliseComponent(component) {
    return component.replace(/^core_/u, "");
  }
  /**
   * Generate or validate an ID for a request within its authentication context.
   */
  #getRequestId(authenticationContext, id) {
    if (id === null) {
      let requestId;
      do {
        requestId = this.#getGuid();
      } while (this.#requestMap.get(authenticationContext)?.has(requestId));
      return requestId;
    }
    if (this.#requestMap.get(authenticationContext)?.has(id)) {
      throw new Error(`Request with ID ${id} already exists.`);
    }
    return id;
  }
  /**
   * Add a request to the batch queue.
   *
   * Requests with a cache key (greater than 1) are cacheable assets and bypass the queue, unless
   * the `batchApiAssetRequests` config value is enabled.
   *
   * @param component The frankenstyle component name.
   * @param endpoint The endpoint within the component to call.
   * @param options Request options, including the optional batch request `id`.
   * @returns A promise that resolves to the {@link Response} for this request within the batch.
   */
  async #addRequest(component, endpoint, {
    cachekey = null,
    headers = {},
    params = {},
    body = null,
    method = "GET",
    id = null
  }) {
    if (cachekey !== null && cachekey > 1 && !config.batchApiAssetRequests) {
      if (id !== null) {
        throw new Error("A request ID cannot be specified for a request which bypasses the batcher.");
      }
      return Fetch.request(component, endpoint, {
        cachekey,
        headers,
        params,
        body,
        method
      });
    }
    let requestCount = 0;
    for (const group of this.#requestMap.values()) {
      requestCount += group.size;
    }
    if (requestCount >= 20) {
      void this.execute();
    }
    const requestHeaders = new Headers(headers);
    const authenticationContext = requestHeaders.has("Authorization") ? `authorization:${requestHeaders.get("Authorization")}` : "cookie";
    const requestId = this.#getRequestId(authenticationContext, id);
    const request = Fetch.#getRequest(
      Fetch.#normaliseComponent(component),
      endpoint,
      {
        cachekey,
        headers,
        params,
        method,
        body
      }
    );
    if (!this.#requestMap.has(authenticationContext)) {
      this.#requestMap.set(authenticationContext, /* @__PURE__ */ new Map());
    }
    this.#requestMap.get(authenticationContext)?.set(requestId, request);
    if (this.#batchRequests) {
      if (this.#autoBatchTimeout !== null) {
        this.#queueExecution();
      }
    } else {
      void this.execute();
    }
    return request.promise;
  }
  /**
   * Queue the execution of the batch request after {@link Fetch#autoBatchTimeout} milliseconds.
   */
  #queueExecution() {
    const timeout = this.#autoBatchTimeout;
    if (timeout === null) {
      return;
    }
    if (this.#delayTimer) {
      return;
    }
    this.#delayTimer = setTimeout(() => {
      void this.execute();
    }, timeout);
  }
  /**
   * Build a {@link RequestWrapper} for a given API call.
   *
   * @param component The normalised component name.
   * @param endpoint The endpoint within the component.
   * @param options Request options.
   * @returns A new {@link RequestWrapper}.
   */
  static #getRequest(component, endpoint, {
    cachekey = null,
    headers = {},
    params = {},
    body = null,
    method = "GET"
  }) {
    const urlParts = ["rest", "v2"];
    if (cachekey !== null && cachekey > 1) {
      urlParts.push(`cachekey:${cachekey}`);
    }
    urlParts.push(component, endpoint);
    const url = new URL(`${config.apibase}/${urlParts.join("/").replaceAll("//", "/")}`);
    const options = {
      method,
      headers: {
        ...headers,
        Accept: "application/json",
        "Content-Type": "application/json",
        pageparent: config.traceId ?? ""
      },
      signal: getGlobalAbortSignal()
    };
    for (const [key, value] of Object.entries(params)) {
      url.searchParams.append(key, value);
    }
    if (body !== null) {
      if (body instanceof FormData) {
        options.body = body;
      } else if (typeof body === "object") {
        options.body = JSON.stringify(body);
      } else {
        options.body = body;
      }
    }
    return new RequestWrapper(new Request(url, options));
  }
  /**
   * Build the compiled batch {@link Request}.
   *
   * Batch requests are formatted in a format as close as possible to ODATA 4.0.
   *
   * @param requestMap The map of request IDs to {@link RequestWrapper}s to include in the batch.
   * @returns A promise that resolves to the compiled batch {@link Request}.
   */
  async #getRequests(requestMap) {
    const boundary = this.#getGuid();
    const getBoundaryMarker = /* @__PURE__ */ __name((finalDelimiter = false) => `--${boundary}${finalDelimiter ? "--" : ""}\r
`, "getBoundaryMarker");
    const getRequestHeader = /* @__PURE__ */ __name(() => [
      getBoundaryMarker(),
      "Content-Type: application/http\r\n\r\n"
    ], "getRequestHeader");
    const requestBodies = await Promise.all([...requestMap].map(async ([id, { request }]) => {
      const thisBody = [
        ...getRequestHeader(),
        `${request.method} ${request.url}\r
`,
        `Content-Type: ${request.headers.get("Content-Type")}\r
`,
        `Content-ID: ${id}\r
`
      ];
      request.headers.forEach((value, name) => {
        if (name.toLowerCase() !== "content-type" && name.toLowerCase() !== "content-id") {
          thisBody.push(`${name}: ${value}\r
`);
        }
      });
      if (request.body !== null) {
        return [...thisBody, "\r\n", await request.text(), "\r\n"];
      }
      return [...thisBody, "\r\n"];
    }));
    const prologue = [];
    for (const { request } of requestMap.values()) {
      prologue.push(`# Requesting ${request.method} ${request.url}\r
`);
    }
    const body = [
      // Add a prologue to aid debugging.
      ...prologue,
      // Now add the actual request bodies.
      ...requestBodies.map((requestBody) => requestBody.join("")),
      // The final boundary marker.
      getBoundaryMarker(true)
      // No epilogue here. Our API does support one if we need to add one later.
    ];
    return new Request(
      `${config.apibase}/$batch`,
      {
        body: body.join(""),
        method: "POST",
        headers: {
          "Content-Type": `multipart/mixed;boundary=${boundary}`
        },
        signal: getGlobalAbortSignal()
      }
    );
  }
  /**
   * Parse a single sub-response's raw text and return a {@link Response} object for it.
   *
   * @param text The raw text of the sub-response, as extracted from the batch response body.
   * @returns The parsed {@link Response}.
   */
  #getResponseFromText(text) {
    const [, headerText, ...bodyParts] = text.split("\r\n\r\n").map((part) => part.trim());
    const body = bodyParts.length > 0 ? bodyParts.join("\r\n\r\n").trim() : null;
    const headers = headerText.split("\r\n");
    const statusLine = headers.shift() ?? "";
    const statusRegexp = /HTTP\/(?<protocol>[^ ]*) (?<status>\d{3}) (?<statusText>.*)$/u;
    const matches = statusRegexp.exec(statusLine);
    const { status, statusText } = matches?.groups ?? { status: void 0, statusText: void 0 };
    const headerList = headers.filter((header) => header.includes(":")).map((header) => {
      const index = header.indexOf(":");
      return [header.slice(0, index).trim(), header.slice(index + 1).trim()];
    });
    return new Response(body, {
      status: Number(status),
      statusText,
      headers: new Headers(headerList)
    });
  }
  /**
   * Process the raw batch {@link Response} into a list of per-request response text, or a parsed JSON object.
   *
   * @param response The raw batch {@link Response}.
   * @returns The list of raw per-request response text (for `multipart/mixed` responses).
   */
  async #processBatchResponse(response) {
    const contentType = response.headers.get("Content-Type") ?? "";
    if (contentType.startsWith("multipart/mixed")) {
      const [, boundaryPart] = contentType.split(";").map((part) => part.trim());
      const boundary = boundaryPart.replace("boundary=", "").trim();
      const responseBody = await response.text();
      return responseBody.split(new RegExp(`(?:--${boundary}(?:--)?(?:\r
)?)`, "gmu")).filter((value) => value.length > 0);
    }
    throw new Error(`Unknown response type '${contentType}'`);
  }
  /**
   * Generate a GUID.
   *
   * Note: This is not a true GUID, but is random enough for our purposes.
   *
   * @returns A pseudo-random GUID string.
   */
  #getGuid() {
    if (typeof crypto !== "undefined" && typeof crypto.randomUUID === "function") {
      return crypto.randomUUID();
    }
    let d = Date.now();
    const guid = "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replaceAll(/[xy]/gu, (c) => {
      const r = Math.trunc((d + Math.random() * 16) % 16);
      d = Math.floor(d / 16);
      return (c === "x" ? r : r % 4 + 8).toString(16);
    });
    return guid;
  }
}
export {
  Fetch as default
};
//# sourceMappingURL=fetch.dev.js.map
