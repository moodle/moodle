var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * Sort-order (dates / courses) selector for the Timeline block.
 *
 * Matches the DOM structure of the legacy nav-view-selector.mustache template.
 *
 * @module     block_timeline/nav/ViewSelector
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState, useEffect, useId } from "react";
import String from "@moodle/lms/core/String";
import { getString } from "@moodle/lms/core/stringUtils";
const SPAN_ID = "timeline-view-selector-current-selection";
const VIEW_OPTIONS = [
  { name: "sortbydates", labelKey: "sortbydates" },
  { name: "sortbycourses", labelKey: "sortbycourses" }
];
function ViewSelector({ activeOrder, onChange }) {
  const uid = useId().replace(/:/g, "");
  const menuId = "menusortby";
  const datesId = `view_dates_${uid}`;
  const coursesId = `view_courses_${uid}`;
  const panelId = {
    sortbydates: datesId,
    sortbycourses: coursesId
  };
  const [buttonLabel, setButtonLabel] = useState("");
  const [itemLabels, setItemLabels] = useState({});
  useEffect(() => {
    getString("ariaviewselector", "block_timeline").then(setButtonLabel);
    VIEW_OPTIONS.forEach((opt) => {
      getString(opt.labelKey, "block_timeline").then((label) => getString("ariaviewselectoroption", "block_timeline", label)).then((ariaLabel) => setItemLabels((prev) => ({ ...prev, [opt.name]: ariaLabel })));
    });
  }, []);
  const activeOption = VIEW_OPTIONS.find((o) => o.name === activeOrder) ?? VIEW_OPTIONS[0];
  return /* @__PURE__ */ jsxDEV("div", { "data-region": "view-selector", className: "dropdown mb-1", children: [
    /* @__PURE__ */ jsxDEV(
      "button",
      {
        type: "button",
        className: "btn btn-outline-secondary dropdown-toggle icon-no-margin",
        "data-bs-toggle": "dropdown",
        "aria-haspopup": "true",
        "aria-label": buttonLabel,
        "aria-controls": menuId,
        title: buttonLabel,
        "aria-describedby": SPAN_ID,
        children: /* @__PURE__ */ jsxDEV("span", { id: SPAN_ID, "data-active-item-text": "", children: /* @__PURE__ */ jsxDEV(String, { identifier: activeOption.labelKey, component: "block_timeline", children: "" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
          lineNumber: 92,
          columnNumber: 21
        }, this) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
          lineNumber: 91,
          columnNumber: 17
        }, this)
      },
      void 0,
      false,
      {
        fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
        lineNumber: 81,
        columnNumber: 13
      },
      this
    ),
    /* @__PURE__ */ jsxDEV(
      "div",
      {
        id: menuId,
        role: "tablist",
        className: "dropdown-menu dropdown-menu-end",
        "data-show-active-item": "",
        children: VIEW_OPTIONS.map((option) => /* @__PURE__ */ jsxDEV(
          "a",
          {
            className: `dropdown-item${activeOrder === option.name ? " active dropdown-item-active" : ""}`,
            href: `#${panelId[option.name]}`,
            "data-filtername": option.name,
            "aria-current": activeOrder === option.name ? "true" : void 0,
            "aria-label": itemLabels[option.name],
            "aria-controls": panelId[option.name],
            role: "tab",
            onClick: (e) => {
              e.preventDefault();
              onChange(option.name);
            },
            children: /* @__PURE__ */ jsxDEV(String, { identifier: option.labelKey, component: "block_timeline", children: "" }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
              lineNumber: 117,
              columnNumber: 25
            }, this)
          },
          option.name,
          false,
          {
            fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
            lineNumber: 103,
            columnNumber: 21
          },
          this
        ))
      },
      void 0,
      false,
      {
        fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
        lineNumber: 96,
        columnNumber: 13
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
    lineNumber: 80,
    columnNumber: 9
  }, this);
}
__name(ViewSelector, "ViewSelector");
export {
  ViewSelector as default
};
//# sourceMappingURL=ViewSelector.dev.js.map
