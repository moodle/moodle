var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * Promise-based AMD module loader.
 *
 * @module     core/amd
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function requireAsync(moduleId) {
  return new Promise((resolve, reject) => {
    requirejs([moduleId], (mod) => resolve(mod), reject);
  });
}
__name(requireAsync, "requireAsync");
function requireManyAsync(moduleIds) {
  return new Promise((resolve, reject) => {
    requirejs(moduleIds, (...modules) => resolve(modules), reject);
  });
}
__name(requireManyAsync, "requireManyAsync");
export {
  requireAsync,
  requireManyAsync
};
//# sourceMappingURL=amd.dev.js.map
