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
  let onCooldown = false;
  let runAgain = false;
  let latestArgs;
  const run = /* @__PURE__ */ __name(function(...args) {
    latestArgs = args;
    if (onCooldown) {
      runAgain = true;
      return;
    }
    func.apply(this, args);
    onCooldown = true;
    setTimeout(() => {
      const recurse = runAgain;
      onCooldown = false;
      runAgain = false;
      if (recurse) {
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
    timeout = setTimeout(async () => {
      const pendingPromise = debounceMap.get(returnedFunction);
      debounceMap.delete(returnedFunction);
      await func.apply(void 0, args);
      pendingPromise?.resolve();
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
  if (component && component !== "moodle" && component !== "core") {
    return component;
  }
  return "core";
}, "getNormalisedComponent");
var utils_default = {
  throttle,
  debounce,
  getNormalisedComponent
};
export {
  debounce,
  utils_default as default,
  getNormalisedComponent,
  throttle
};
//# sourceMappingURL=utils.dev.js.map
