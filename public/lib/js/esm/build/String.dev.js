var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
import { Suspense, use } from "react";
import { requireAsync } from "@moodle/lms/core/amd";
import config from "./config";
import { localStore } from "./Storage";
const promiseCache = /* @__PURE__ */ new Map();
const stringPromiseCache = /* @__PURE__ */ new Map();
const getCacheKey = /* @__PURE__ */ __name((key, component, lang) => `core_str/${key}/${component}/${lang}`, "getCacheKey");
const getRequestedStrings = /* @__PURE__ */ __name((requests) => {
  const stringPromises = new Array(requests.length);
  const pendingFetches = [];
  for (let i = 0; i < requests.length; i++) {
    const { key, component: rawComponent = "core", param = null, lang = config.language } = requests[i];
    const component = rawComponent || "core";
    const cacheKey = getCacheKey(key, component, lang);
    if (M.str[component]?.[key] !== void 0) {
      const promise = Promise.resolve(M.util.get_string(key, component, param));
      promiseCache.set(cacheKey, promise);
      stringPromises[i] = promise;
      continue;
    }
    const cached = localStore.get(cacheKey);
    if (cached !== null) {
      if (!M.str[component]) {
        M.str[component] = {};
      }
      M.str[component][key] = cached;
      const promise = Promise.resolve(M.util.get_string(key, component, param));
      promiseCache.set(cacheKey, promise);
      stringPromises[i] = promise;
      continue;
    }
    if (promiseCache.has(cacheKey)) {
      stringPromises[i] = promiseCache.get(cacheKey).then(
        () => M.util.get_string(key, component, param)
      );
      continue;
    }
    const fetchPromise = new Promise((resolve, reject) => {
      pendingFetches.push({
        request: {
          methodname: "core_get_string",
          args: { stringid: key, stringparams: [], component, lang }
        },
        resolve,
        reject
      });
    });
    promiseCache.set(cacheKey, fetchPromise);
    stringPromises[i] = fetchPromise.then((str) => {
      if (!M.str[component]) {
        M.str[component] = {};
      }
      M.str[component][key] = str;
      localStore.set(cacheKey, str);
      return M.util.get_string(key, component, param);
    });
  }
  if (pendingFetches.length > 0) {
    const ajaxRequests = pendingFetches.map((pf) => pf.request);
    requireAsync("core/ajax").then(
      (ajax) => {
        const jqPromises = ajax.call(
          ajaxRequests,
          true,
          false,
          false,
          0,
          config.langrev
        );
        jqPromises.forEach((jqp, j) => {
          jqp.then(
            // eslint-disable-line promise/no-nesting
            (str) => pendingFetches[j].resolve(str),
            (err) => pendingFetches[j].reject(err)
          );
        });
        return ajax;
      },
      (err) => {
        pendingFetches.forEach((pf) => pf.reject(err));
      }
    );
  }
  return stringPromises;
}, "getRequestedStrings");
const getStrings = /* @__PURE__ */ __name((requests) => Promise.all(getRequestedStrings(requests)), "getStrings");
const cacheStrings = /* @__PURE__ */ __name((strings) => {
  for (const { key, component = "core", value, lang = config.language } of strings) {
    const cacheKey = getCacheKey(key, component, lang);
    if (!M.str[component]) {
      M.str[component] = {};
    }
    if (!(key in M.str[component])) {
      M.str[component][key] = value;
    }
    localStore.set(cacheKey, value);
    if (!promiseCache.has(cacheKey)) {
      promiseCache.set(cacheKey, Promise.resolve(value));
    }
  }
}, "cacheStrings");
const getString = /* @__PURE__ */ __name((identifier, component = "core", params) => {
  const key = `${component}::${identifier}::${JSON.stringify(params)}`;
  if (!stringPromiseCache.has(key)) {
    stringPromiseCache.set(
      key,
      getRequestedStrings([{ key: identifier, component, param: params }])[0]
    );
  }
  return stringPromiseCache.get(key);
}, "getString");
const resetStringCache = /* @__PURE__ */ __name(() => {
  stringPromiseCache.clear();
  promiseCache.clear();
}, "resetStringCache");
function StringInner({ identifier, component, params }) {
  return /* @__PURE__ */ jsxDEV(Fragment, { children: use(getString(identifier, component, params)) }, void 0, false, {
    fileName: "public/lib/js/esm/src/String.tsx",
    lineNumber: 273,
    columnNumber: 12
  }, this);
}
__name(StringInner, "StringInner");
function String({ children, identifier, component = "core", params }) {
  return /* @__PURE__ */ jsxDEV(Suspense, { fallback: children ?? `${identifier}, ${component}`, children: /* @__PURE__ */ jsxDEV(StringInner, { identifier, component, params }, void 0, false, {
    fileName: "public/lib/js/esm/src/String.tsx",
    lineNumber: 279,
    columnNumber: 13
  }, this) }, void 0, false, {
    fileName: "public/lib/js/esm/src/String.tsx",
    lineNumber: 278,
    columnNumber: 9
  }, this);
}
__name(String, "String");
var String_default = String;
export {
  cacheStrings,
  String_default as default,
  getRequestedStrings,
  getString,
  getStrings,
  resetStringCache
};
//# sourceMappingURL=String.dev.js.map
