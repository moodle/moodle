var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
/**
 * ESM wrapper for the core/ajax AMD module.
 *
 * @module     core/ajax
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { requireAsync } from "@moodle/lms/core/amd";
function isMoodleAjaxError(err) {
  return typeof err === "object" && err !== null && "message" in err && "errorcode" in err;
}
__name(isMoodleAjaxError, "isMoodleAjaxError");
const ajax = await requireAsync("core/ajax");
function toNativePromise(jqPromise) {
  return new Promise((resolve, reject) => {
    jqPromise.then(resolve, reject);
  });
}
__name(toNativePromise, "toNativePromise");
function fetchOne(request, isAsync = true, loginrequired = true, nosessionupdate = false) {
  const [jqPromise] = ajax.call([request], isAsync, loginrequired, nosessionupdate);
  return toNativePromise(jqPromise);
}
__name(fetchOne, "fetchOne");
function fetchMany(requests, isAsync = true, loginrequired = true, nosessionupdate = false) {
  return Promise.all(
    ajax.call(requests, isAsync, loginrequired, nosessionupdate).map((jqPromise) => toNativePromise(jqPromise))
  );
}
__name(fetchMany, "fetchMany");
export {
  fetchMany,
  fetchOne,
  isMoodleAjaxError
};
//# sourceMappingURL=ajax.dev.js.map
