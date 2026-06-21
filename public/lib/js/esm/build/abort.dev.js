var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Global Abort Controller used in the Fetch API.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
const getGlobalAbortSignal = /* @__PURE__ */ __name(() => {
  return window.globalAbortController.signal;
}, "getGlobalAbortSignal");
const abortGlobalFetches = /* @__PURE__ */ __name(() => {
  window.globalAbortController?.abort();
}, "abortGlobalFetches");
const resetGlobalAbortController = /* @__PURE__ */ __name(() => {
  window.globalAbortController = new AbortController();
}, "resetGlobalAbortController");
resetGlobalAbortController();
var abort_default = getGlobalAbortSignal;
export {
  abortGlobalFetches,
  abort_default as default,
  getGlobalAbortSignal,
  resetGlobalAbortController
};
//# sourceMappingURL=abort.dev.js.map
