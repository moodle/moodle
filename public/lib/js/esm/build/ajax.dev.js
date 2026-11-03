var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Standard Ajax wrapper for Moodle web service calls.
 *
 * Calls the central Ajax script which can invoke any existing web service
 * using the current session. Supports batching multiple requests into a
 * single HTTP call.
 *
 * @module     core/ajax
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
import { getGlobalAbortSignal } from "./abort";
import config from "@moodle/lms/core/config";
import Pending from "@moodle/lms/core/pending";
import log from "@moodle/lms/core/log";
import { redirect } from "@moodle/lms/core/location";
import { relativeUrl } from "@moodle/lms/core/url";
function isMoodleAjaxError(error) {
  return typeof error === "object" && error !== null && "message" in error && "errorcode" in error;
}
__name(isMoodleAjaxError, "isMoodleAjaxError");
const MAX_URL_LENGTH = 2e3;
let isUnloading = false;
if (typeof window !== "undefined") {
  window.addEventListener("beforeunload", () => {
    isUnloading = true;
  });
}
function buildRequest(requestData, options) {
  const { loginrequired, nosessionupdate, cachekey } = options;
  const methodInfo = requestData.map((r) => r.methodname);
  const requestInfo = methodInfo.length <= 5 ? methodInfo.sort().join(",") : `${methodInfo.length}-method-calls`;
  const ajaxRequestData = JSON.stringify(requestData);
  let script;
  let url;
  let method = "POST";
  if (loginrequired) {
    script = "service.php";
    url = `${config.wwwroot}/lib/ajax/${script}?sesskey=${config.sesskey}&info=${requestInfo}`;
  } else {
    script = "service-nologin.php";
    url = `${config.wwwroot}/lib/ajax/${script}?info=${requestInfo}`;
    if (cachekey !== null) {
      url += `&cachekey=${cachekey}`;
      method = "GET";
    }
  }
  if (nosessionupdate) {
    url += "&nosessionupdate=true";
  }
  const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
    pageparent: config.traceId ?? ""
  };
  let body;
  if (method === "POST") {
    body = ajaxRequestData;
  } else {
    const urlWithArgs = `${url}&args=${encodeURIComponent(ajaxRequestData)}`;
    if (urlWithArgs.length > MAX_URL_LENGTH) {
      method = "POST";
      body = ajaxRequestData;
    } else {
      url = urlWithArgs;
    }
  }
  const init = {
    method,
    headers,
    credentials: "same-origin",
    signal: getGlobalAbortSignal()
  };
  if (body !== void 0) {
    init.body = body;
  }
  return { url, init };
}
__name(buildRequest, "buildRequest");
function processResponse(responses, resolvers, nosessionupdate) {
  if ("error" in responses && responses.error && !Array.isArray(responses)) {
    for (const { reject } of resolvers) {
      reject(responses);
    }
    return;
  }
  const items = responses;
  let exception = null;
  for (const [index, resolver] of resolvers.entries()) {
    const response = items[index];
    if (response === void 0) {
      exception = new Error("missing response");
      break;
    }
    if (response.error !== false) {
      exception = response.exception ?? new Error("Unknown error");
      break;
    }
    resolver.resolve(response.data);
  }
  if (exception !== null) {
    if (isMoodleAjaxError(exception) && exception.errorcode === "servicerequireslogin" && !nosessionupdate) {
      redirect(relativeUrl("/login/index.php"));
    } else {
      for (const { reject } of resolvers) {
        reject(exception);
      }
    }
  }
}
__name(processResponse, "processResponse");
function performFetch(requests, options = {}) {
  const {
    loginrequired = true,
    nosessionupdate = false,
    timeout = 0,
    cachekey = null
  } = options;
  const resolvedOptions = {
    loginrequired,
    nosessionupdate,
    timeout,
    cachekey: cachekey !== null && cachekey > 0 ? cachekey : null
  };
  const requestData = requests.map((request, index) => ({
    index,
    methodname: request.methodname,
    args: request.args
  }));
  const resolvers = [];
  const promises = requests.map(async () => {
    let outerResolve;
    let outerReject;
    const promise = new Promise((resolve, reject) => {
      outerResolve = resolve;
      outerReject = reject;
    });
    resolvers.push({ resolve: outerResolve, reject: outerReject });
    return promise;
  });
  const { url, init } = buildRequest(requestData, resolvedOptions);
  const pendingPromise = new Pending("core/ajax:call");
  let controller;
  let timeoutId;
  if (timeout > 0) {
    const timeoutController = new AbortController();
    controller = timeoutController;
    init.signal = timeoutController.signal;
    timeoutId = setTimeout(() => {
      timeoutController.abort();
    }, timeout);
    getGlobalAbortSignal().addEventListener("abort", () => {
      controller.abort();
    });
  }
  fetch(url, init).then(async (response) => {
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    return response.json();
  }).then((data) => {
    processResponse(data, resolvers, nosessionupdate);
    return data;
  }).catch((error) => {
    if (isUnloading) {
      log.error("Page unloaded.");
      log.error(error);
    } else {
      for (const { reject } of resolvers) {
        reject(error);
      }
    }
  }).finally(() => {
    if (timeoutId) {
      clearTimeout(timeoutId);
    }
    pendingPromise.resolve();
  });
  return promises;
}
__name(performFetch, "performFetch");
async function fetchOne(request, options = {}) {
  return performFetch([request], options)[0];
}
__name(fetchOne, "fetchOne");
async function fetchMany(requests, options = {}) {
  return Promise.all(performFetch(requests, options));
}
__name(fetchMany, "fetchMany");
export {
  fetchMany,
  fetchOne,
  isMoodleAjaxError,
  performFetch
};
//# sourceMappingURL=ajax.dev.js.map
