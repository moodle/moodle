var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * Day-filter dropdown for the Timeline block.
 *
 * Matches the DOM structure of the legacy nav-day-filter.mustache template.
 *
 * @module     block_timeline/nav/DayFilter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState, useEffect } from "react";
import String from "@moodle/lms/core/String";
import { getString } from "@moodle/lms/core/stringUtils";
const MENU_ID = "menudayfilter";
const SPAN_ID = "timeline-day-filter-current-selection";
const GROUP_ID = "duedatefiltergrouplabel";
const TOP_OPTIONS = [
  { name: "all", labelKey: "all", labelComponent: "core", dataFrom: "-14" },
  { name: "overdue", labelKey: "overdue", labelComponent: "block_timeline", dataFrom: "-14", dataTo: "1" }
];
const GROUP_OPTIONS = [
  { name: "next7days", labelKey: "next7days", labelComponent: "block_timeline", dataFrom: "0", dataTo: "7" },
  { name: "next30days", labelKey: "next30days", labelComponent: "block_timeline", dataFrom: "0", dataTo: "30" },
  { name: "next3months", labelKey: "next3months", labelComponent: "block_timeline", dataFrom: "0", dataTo: "90" },
  { name: "next6months", labelKey: "next6months", labelComponent: "block_timeline", dataFrom: "0", dataTo: "180" }
];
const ALL_OPTIONS = [...TOP_OPTIONS, ...GROUP_OPTIONS];
function DayFilter({ activeFilter, onChange }) {
  const [buttonLabel, setButtonLabel] = useState("");
  const [itemLabels, setItemLabels] = useState({});
  useEffect(() => {
    getString("ariadayfilter", "block_timeline").then(setButtonLabel);
    ALL_OPTIONS.forEach((opt) => {
      getString(opt.labelKey, opt.labelComponent).then((label) => getString("ariadayfilteroption", "block_timeline", label)).then((ariaLabel) => setItemLabels((prev) => ({ ...prev, [opt.name]: ariaLabel })));
    });
  }, []);
  const activeOption = ALL_OPTIONS.find((o) => o.name === activeFilter) ?? ALL_OPTIONS[0];
  const renderItem = /* @__PURE__ */ __name((option) => /* @__PURE__ */ jsxDEV(
    "a",
    {
      className: `dropdown-item${activeFilter === option.name ? " active dropdown-item-active" : ""}`,
      href: "#",
      "data-from": option.dataFrom,
      ...option.dataTo !== void 0 ? { "data-to": option.dataTo } : {},
      "data-filtername": option.name,
      "aria-current": activeFilter === option.name ? "true" : void 0,
      "aria-label": itemLabels[option.name],
      role: "menuitem",
      onClick: (e) => {
        e.preventDefault();
        onChange(option.name);
      },
      children: /* @__PURE__ */ jsxDEV(String, { identifier: option.labelKey, component: option.labelComponent, children: "" }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
        lineNumber: 101,
        columnNumber: 13
      }, this)
    },
    option.name,
    false,
    {
      fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
      lineNumber: 86,
      columnNumber: 9
    },
    this
  ), "renderItem");
  return /* @__PURE__ */ jsxDEV("div", { "data-region": "day-filter", className: "dropdown mb-1", children: [
    /* @__PURE__ */ jsxDEV(
      "button",
      {
        type: "button",
        className: "btn btn-outline-secondary dropdown-toggle icon-no-margin",
        "data-bs-toggle": "dropdown",
        "aria-haspopup": "true",
        "aria-label": buttonLabel,
        "aria-controls": MENU_ID,
        title: buttonLabel,
        "aria-describedby": SPAN_ID,
        children: /* @__PURE__ */ jsxDEV("span", { id: SPAN_ID, "data-active-item-text": "", children: /* @__PURE__ */ jsxDEV(
          String,
          {
            identifier: activeOption.labelKey,
            component: activeOption.labelComponent,
            children: ""
          },
          void 0,
          false,
          {
            fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
            lineNumber: 118,
            columnNumber: 21
          },
          this
        ) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
          lineNumber: 117,
          columnNumber: 17
        }, this)
      },
      void 0,
      false,
      {
        fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
        lineNumber: 107,
        columnNumber: 13
      },
      this
    ),
    /* @__PURE__ */ jsxDEV("div", { id: MENU_ID, role: "menu", className: "dropdown-menu", "data-show-active-item": "", "data-skip-active-class": "true", children: [
      TOP_OPTIONS.map(renderItem),
      /* @__PURE__ */ jsxDEV("div", { className: "dropdown-divider", role: "separator" }, void 0, false, {
        fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
        lineNumber: 128,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV("div", { role: "group", "aria-labelledby": GROUP_ID, children: [
        /* @__PURE__ */ jsxDEV("div", { className: "h6 dropdown-header", role: "presentation", id: GROUP_ID, children: /* @__PURE__ */ jsxDEV(String, { identifier: "duedate", component: "block_timeline", children: "" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
          lineNumber: 132,
          columnNumber: 25
        }, this) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
          lineNumber: 131,
          columnNumber: 21
        }, this),
        GROUP_OPTIONS.map(renderItem)
      ] }, void 0, true, {
        fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
        lineNumber: 130,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
      lineNumber: 125,
      columnNumber: 13
    }, this)
  ] }, void 0, true, {
    fileName: "public/blocks/timeline/js/esm/src/nav/DayFilter.tsx",
    lineNumber: 106,
    columnNumber: 9
  }, this);
}
__name(DayFilter, "DayFilter");
export {
  DayFilter as default
};
//# sourceMappingURL=DayFilter.dev.js.map
