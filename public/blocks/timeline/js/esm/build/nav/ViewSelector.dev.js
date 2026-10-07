var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * Sort-order (dates / courses) selector for the Timeline block.
 *
 * Matches the DOM structure of the legacy nav-view-selector.mustache template, except for the
 * ARIA roles: this is a dropdown of two sort options, so it uses the menu pattern that DayFilter
 * and Bootstrap's own dropdown JS already implement, rather than the tablist the legacy template
 * declared but never wired up.
 *
 * @module     block_timeline/nav/ViewSelector
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useAriaLabels } from "../common/useAriaLabels";
import { useComposedLabel } from "../common/useComposedLabel";
import String from "@moodle/lms/core/String";
const VIEW_OPTIONS = [
  { name: "sortbydates", labelKey: "sortbydates" },
  { name: "sortbycourses", labelKey: "sortbycourses" }
];
function ViewSelector({ activeOrder, onChange }) {
  const menuId = "menusortby";
  const { buttonLabel: menuLabel, itemLabels } = useAriaLabels("ariaviewselector", "ariaviewselectoroption", VIEW_OPTIONS);
  const activeOption = VIEW_OPTIONS.find((o) => o.name === activeOrder) ?? VIEW_OPTIONS[0];
  const toggleLabel = useComposedLabel("ariaviewselectorbutton", activeOption.labelKey);
  return /* @__PURE__ */ jsxDEV("div", { "data-region": "view-selector", className: "dropdown mb-1", children: [
    /* @__PURE__ */ jsxDEV(
      "button",
      {
        type: "button",
        className: "btn btn-outline-secondary dropdown-toggle icon-no-margin",
        "data-bs-toggle": "dropdown",
        "aria-haspopup": "true",
        "aria-expanded": "false",
        "aria-label": toggleLabel,
        "aria-controls": menuId,
        title: menuLabel,
        children: /* @__PURE__ */ jsxDEV("span", { "data-active-item-text": "", children: /* @__PURE__ */ jsxDEV(String, { identifier: activeOption.labelKey, component: "block_timeline", children: "" }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
          lineNumber: 79,
          columnNumber: 21
        }, this) }, void 0, false, {
          fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
          lineNumber: 78,
          columnNumber: 17
        }, this)
      },
      void 0,
      false,
      {
        fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
        lineNumber: 65,
        columnNumber: 13
      },
      this
    ),
    /* @__PURE__ */ jsxDEV(
      "div",
      {
        id: menuId,
        role: "menu",
        "aria-label": menuLabel,
        className: "dropdown-menu dropdown-menu-end",
        "data-show-active-item": "",
        children: VIEW_OPTIONS.map((option) => /* @__PURE__ */ jsxDEV(
          "a",
          {
            className: `dropdown-item${activeOrder === option.name ? " active dropdown-item-active" : ""}`,
            href: "#",
            "data-filtername": option.name,
            "aria-current": activeOrder === option.name ? "true" : void 0,
            "aria-label": itemLabels[option.name],
            role: "menuitem",
            onClick: (event) => {
              event.preventDefault();
              onChange(option.name);
            },
            children: /* @__PURE__ */ jsxDEV(String, { identifier: option.labelKey, component: "block_timeline", children: "" }, void 0, false, {
              fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
              lineNumber: 104,
              columnNumber: 25
            }, this)
          },
          option.name,
          false,
          {
            fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
            lineNumber: 91,
            columnNumber: 21
          },
          this
        ))
      },
      void 0,
      false,
      {
        fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
        lineNumber: 83,
        columnNumber: 13
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/blocks/timeline/js/esm/src/nav/ViewSelector.tsx",
    lineNumber: 64,
    columnNumber: 9
  }, this);
}
__name(ViewSelector, "ViewSelector");
export {
  ViewSelector as default
};
//# sourceMappingURL=ViewSelector.dev.js.map
