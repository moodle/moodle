var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
/**
 * A course activity date (e.g. "Opens: Monday, 1 January 2024, 12:00 AM").
 *
 * @module     core_course/ActivityDate
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { Badge } from "@moodlehq/design-system";
function ActivityDate({ label, value }) {
  const content = /* @__PURE__ */ jsxDEV(Fragment, { children: [
    /* @__PURE__ */ jsxDEV("span", { children: label }, void 0, false, {
      fileName: "public/course/js/esm/src/ActivityDate.tsx",
      lineNumber: 42,
      columnNumber: 13
    }, this),
    " ",
    /* @__PURE__ */ jsxDEV("span", { children: value }, void 0, false, {
      fileName: "public/course/js/esm/src/ActivityDate.tsx",
      lineNumber: 44,
      columnNumber: 13
    }, this)
  ] }, void 0, true, {
    fileName: "public/course/js/esm/src/ActivityDate.tsx",
    lineNumber: 41,
    columnNumber: 9
  }, this);
  return /* @__PURE__ */ jsxDEV(Badge, { label: content, variant: "secondary", subtle: true }, void 0, false, {
    fileName: "public/course/js/esm/src/ActivityDate.tsx",
    lineNumber: 49,
    columnNumber: 12
  }, this);
}
__name(ActivityDate, "ActivityDate");
export {
  ActivityDate as default
};
//# sourceMappingURL=ActivityDate.dev.js.map
