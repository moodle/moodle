var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * ESM wrapper around the AMD core/str module for loading Moodle language strings.
 *
 * @module     core/String
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { requireAsync } from "@moodle/lms/core/amd";
const stringPromiseCache = /* @__PURE__ */ new Map();
const getString = /* @__PURE__ */ __name((identifier, component = "core", params) => {
  const key = `${component}::${identifier}::${JSON.stringify(params)}`;
  if (!stringPromiseCache.has(key)) {
    stringPromiseCache.set(
      key,
      requireAsync("core/str").then((str) => str.get_string(identifier, component, params))
    );
  }
  return stringPromiseCache.get(key);
}, "getString");
const resetStringCache = /* @__PURE__ */ __name(() => stringPromiseCache.clear(), "resetStringCache");
const cacheStrings = /* @__PURE__ */ __name(async (requests) => {
  const str = await requireAsync("core/str");
  str.cache_strings(requests);
}, "cacheStrings");
export {
  cacheStrings,
  getString,
  resetStringCache
};
//# sourceMappingURL=String.dev.js.map
