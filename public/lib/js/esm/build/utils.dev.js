var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Utility functions.
 *
 * @module     core/utils
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
import Pending from "./pending";
const throttle = /* @__PURE__ */ __name((func, wait) => {
  let isOnCooldown = false;
  let isRunAgain = false;
  let latestArgs;
  const run = /* @__PURE__ */ __name(function(...args) {
    latestArgs = args;
    if (isOnCooldown) {
      isRunAgain = true;
      return;
    }
    func.apply(this, args);
    isOnCooldown = true;
    setTimeout(() => {
      const isRecurse = isRunAgain;
      isOnCooldown = false;
      isRunAgain = false;
      if (isRecurse) {
        run.apply(this, latestArgs);
      }
    }, wait);
  }, "run");
  return run;
}, "throttle");
const debounceMap = /* @__PURE__ */ new Map();
const debounce = /* @__PURE__ */ __name((func, wait, {
  pending = false,
  cancel = false
} = {}) => {
  let timeout = null;
  const returnedFunction = /* @__PURE__ */ __name((...args) => {
    if (pending && !debounceMap.has(returnedFunction)) {
      debounceMap.set(returnedFunction, new Pending("core/utils:debounce"));
    }
    if (timeout !== null) {
      clearTimeout(timeout);
    }
    const flush = /* @__PURE__ */ __name(async () => {
      const pendingPromise = debounceMap.get(returnedFunction);
      debounceMap.delete(returnedFunction);
      await func(...args);
      pendingPromise?.resolve();
    }, "flush");
    timeout = setTimeout(() => {
      void flush();
    }, wait);
  }, "returnedFunction");
  if (cancel) {
    returnedFunction.cancel = () => {
      const pendingPromise = debounceMap.get(returnedFunction);
      pendingPromise?.resolve();
      if (timeout !== null) {
        clearTimeout(timeout);
      }
    };
  }
  return returnedFunction;
}, "debounce");
const getNormalisedComponent = /* @__PURE__ */ __name((component) => {
  if (component !== void 0 && component !== null && !["", "moodle", "core"].includes(component)) {
    return component;
  }
  return "core";
}, "getNormalisedComponent");
const utils = {
  throttle,
  debounce,
  getNormalisedComponent
};
var utils_default = utils;
export {
  debounce,
  utils_default as default,
  getNormalisedComponent,
  throttle
};
//# sourceMappingURL=utils.dev.js.map
