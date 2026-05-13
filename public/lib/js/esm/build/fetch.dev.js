var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * The core/fetch module allows you to make web service requests to the Moodle REST API.
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
 */
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
  /**
   * Make a single request to the Moodle API.
   *
   * @param component The frankenstyle component name.
   * @param action    The component action to perform.
   * @param options   Request options (params, body, method, headers, cachekey).
   * @returns A promise that resolves to the {@link Response}.
   */
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
      { headers, params, method, body, cachekey }
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
   * @param action    The component action to perform.
   * @param options   Optional query-string parameters.
   */
  static performGet(component, action, { cachekey = null, headers = {}, params = {} } = {}) {
    return this.request(component, action, { cachekey, headers, params, method: "GET" });
  }
  /**
   * Perform a HEAD request.
   *
   * @param component The frankenstyle component name.
   * @param action    The component action to perform.
   * @param options   Optional query-string parameters.
   */
  static performHead(component, action, { headers = {}, params = {} } = {}) {
    return this.request(component, action, { headers, params, method: "HEAD" });
  }
  /**
   * Perform a POST request.
   *
   * @param component The frankenstyle component name.
   * @param action    The component action to perform.
   * @param options   The request body and optional headers.
   */
  static performPost(component, action, { headers = {}, body }) {
    return this.request(component, action, { headers, body, method: "POST" });
  }
  /**
   * Perform a PUT request.
   *
   * @param component The frankenstyle component name.
   * @param action    The component action to perform.
   * @param options   The request body and optional headers.
   */
  static performPut(component, action, { headers = {}, body }) {
    return this.request(component, action, { headers, body, method: "PUT" });
  }
  /**
   * Perform a PATCH request.
   *
   * @param component The frankenstyle component name.
   * @param action    The component action to perform.
   * @param options   The request body and optional headers.
   */
  static performPatch(component, action, { headers = {}, body }) {
    return this.request(component, action, { headers, body, method: "PATCH" });
  }
  /**
   * Perform a DELETE request.
   *
   * @param component The frankenstyle component name.
   * @param action    The component action to perform.
   * @param options   Optional query-string parameters and/or body.
   */
  static performDelete(component, action, { headers = {}, params = {}, body = null } = {}) {
    return this.request(component, action, { headers, body, params, method: "DELETE" });
  }
  /**
   * Normalise a component name by stripping the `core_` prefix.
   */
  static #normaliseComponent(component) {
    return component.replace(/^core_/, "");
  }
  /**
   * Build a {@link RequestWrapper} for a given API call.
   *
   * @param component The normalised component name.
   * @param endpoint  The endpoint within the component.
   * @param options   Request options.
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
    if (cachekey && cachekey > 1) {
      urlParts.push(`cachekey:${cachekey}`);
    }
    urlParts.push(component, endpoint);
    const url = new URL(`${config.apibase}/${urlParts.join("/").replaceAll("//", "/")}`);
    const options = {
      method,
      headers: {
        ...headers,
        "Accept": "application/json",
        "Content-Type": "application/json",
        "pageparent": config.traceId || ""
      }
    };
    Object.entries(params).forEach(([key, value]) => {
      url.searchParams.append(key, value);
    });
    if (body) {
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
}
export {
  Fetch as default
};
//# sourceMappingURL=fetch.dev.js.map
