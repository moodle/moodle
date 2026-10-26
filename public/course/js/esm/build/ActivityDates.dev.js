var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
/**
 * The dates of a course activity, displayed as badges.
 *
 * @module     core_course/ActivityDates
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import ActivityDate from "@moodle/lms/core_course/ActivityDate";
function ActivityDates({ dates }) {
  return /* @__PURE__ */ jsxDEV(Fragment, { children: dates.map((date, index) => /* @__PURE__ */ jsxDEV(ActivityDate, { ...date }, index, false, {
    fileName: "public/course/js/esm/src/ActivityDates.tsx",
    lineNumber: 40,
    columnNumber: 41
  }, this)) }, void 0, false, {
    fileName: "public/course/js/esm/src/ActivityDates.tsx",
    lineNumber: 39,
    columnNumber: 9
  }, this);
}
__name(ActivityDates, "ActivityDates");
export {
  ActivityDates as default
};
//# sourceMappingURL=ActivityDates.dev.js.map
