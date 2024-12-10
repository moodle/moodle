var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import config from "./config";
import { localStore } from "./storage";
import Fetch from "@moodle/lms/core/fetch";
const promiseCache = /* @__PURE__ */ new Map();
const stringPromiseCache = /* @__PURE__ */ new Map();
const getCacheKey = /* @__PURE__ */ __name((key, component, lang) => `core_str/${key}/${component}/${lang}`, "getCacheKey");
const getResolvedPromise = /* @__PURE__ */ __name((value) => (
  // React reads `status` and `value` to return the result synchronously instead of suspending for a render.
  Object.assign(Promise.resolve(value), { status: "fulfilled", value })
), "getResolvedPromise");
const getRequestedStrings = /* @__PURE__ */ __name((requests) => {
  const stringPromises = Array.from({ length: requests.length });
  const pendingFetches = [];
  for (const [index, request] of requests.entries()) {
    const { key, param: parameter = null, lang = config.language } = request;
    const requestedComponent = request.component ?? "";
    const component = requestedComponent === "" ? "core" : requestedComponent;
    const cacheKey = getCacheKey(key, component, lang);
    if (M.str[component]?.[key] !== void 0) {
      const promise = getResolvedPromise(M.util.get_string(key, component, parameter));
      promiseCache.set(cacheKey, promise);
      stringPromises[index] = promise;
      continue;
    }
    const cached = localStore.get(cacheKey);
    if (cached !== null) {
      if (M.str[component] === void 0) {
        M.str[component] = {};
      }
      M.str[component][key] = cached;
      const promise = getResolvedPromise(M.util.get_string(key, component, parameter));
      promiseCache.set(cacheKey, promise);
      stringPromises[index] = promise;
      continue;
    }
    if (promiseCache.has(cacheKey)) {
      stringPromises[index] = promiseCache.get(cacheKey).then(() => M.util.get_string(key, component, parameter));
      continue;
    }
    const fetchPromise = new Promise((resolve, reject) => {
      pendingFetches.push({
        component,
        key,
        lang,
        resolve,
        reject
      });
    });
    promiseCache.set(cacheKey, fetchPromise);
    stringPromises[index] = fetchPromise.then((string_) => {
      if (M.str[component] === void 0) {
        M.str[component] = {};
      }
      M.str[component][key] = string_;
      localStore.set(cacheKey, string_);
      return M.util.get_string(key, component, parameter);
    });
  }
  if (pendingFetches.length > 0) {
    const batcher = Fetch.getBatcher();
    for (const { component, key, lang, resolve, reject } of pendingFetches) {
      batcher.performGet(
        "core",
        `/strings/${lang}/${component}/${key}`,
        { cachekey: config.langrev }
      ).then(async (response) => response.json()).then(({ strings }) => strings).then((strings) => {
        resolve(strings[`${component}/${key}`]);
      }).catch((error) => {
        reject(error);
      });
    }
  }
  return stringPromises;
}, "getRequestedStrings");
const getStrings = /* @__PURE__ */ __name(async (requests) => Promise.all(getRequestedStrings(requests)), "getStrings");
const getComponentStrings = /* @__PURE__ */ __name(async (component, lang = config.language) => Fetch.performGet(
  "core",
  `/strings/${lang}/${component}`,
  { cachekey: config.langrev }
).then(async (response) => response.json()).then(({ strings }) => strings).then((strings) => {
  cacheStrings(Object.entries(strings).map(([identifier, value]) => {
    const regex = /^(?<component>[^/]+)\/(?<stringid>[^/]+)$/u;
    const match = regex.exec(identifier);
    return {
      component: match?.groups?.component ?? component,
      key: match?.groups?.stringid ?? identifier,
      value,
      lang
    };
  }));
}).catch(() => null), "getComponentStrings");
const cacheStrings = /* @__PURE__ */ __name((strings) => {
  for (const { key, component = "core", value, lang = config.language } of strings) {
    const cacheKey = getCacheKey(key, component, lang);
    if (M.str[component] === void 0) {
      M.str[component] = {};
    }
    if (M.str[component][key] === void 0) {
      M.str[component][key] = value;
    }
    localStore.set(cacheKey, value);
    if (!promiseCache.has(cacheKey)) {
      promiseCache.set(cacheKey, Promise.resolve(value));
    }
  }
}, "cacheStrings");
const getString = /* @__PURE__ */ __name((identifier, component = "core", parameters) => {
  const key = `${component}::${identifier}::${JSON.stringify(parameters)}`;
  if (!stringPromiseCache.has(key)) {
    stringPromiseCache.set(
      key,
      getRequestedStrings([{ key: identifier, component, param: parameters }])[0]
    );
  }
  return stringPromiseCache.get(key);
}, "getString");
const resetStringCache = /* @__PURE__ */ __name(() => {
  stringPromiseCache.clear();
  promiseCache.clear();
}, "resetStringCache");
export {
  cacheStrings,
  getComponentStrings,
  getRequestedStrings,
  getString,
  getStrings,
  resetStringCache
};
//# sourceMappingURL=stringUtils.dev.js.map
