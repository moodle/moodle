var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * Activity visibility dropdown displayed on every activity card in edit mode.
 *
 * @module     core_course/local/cm/VisibilityDropdown
 * @copyright  2026 Muhammad Arnaldo <muhammad.arnaldo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useCallback, useEffect, useMemo, useRef, useState, useSyncExternalStore } from "react";
import { Dropdown, DropdownItemSelect, DropdownTrigger } from "@moodlehq/design-system";
import { requireAsync } from "@moodle/lms/core/amd";
import Pending from "@moodle/lms/core/pending";
const pendingFocus = /* @__PURE__ */ new Set();
const useIconOnly = /* @__PURE__ */ __name(() => {
  const mediaQueryList = useMemo(() => window.matchMedia("(max-width: 575.98px)"), []);
  const subscribe = useCallback((onStoreChange) => {
    mediaQueryList.addEventListener("change", onStoreChange);
    return () => mediaQueryList.removeEventListener("change", onStoreChange);
  }, [mediaQueryList]);
  return useSyncExternalStore(subscribe, () => mediaQueryList.matches);
}, "useIconOnly");
function VisibilityDropdown({ cmid, value, options }) {
  const [open, setOpen] = useState(false);
  const iconOnly = useIconOnly();
  const [current, setCurrent] = useState(value);
  const dropdownRef = useRef(null);
  const itemRefs = useRef([]);
  const getTrigger = /* @__PURE__ */ __name(() => dropdownRef.current?.querySelector(".activity-visibility-trigger"), "getTrigger");
  useEffect(() => {
    if (pendingFocus.delete(cmid)) {
      getTrigger()?.focus();
    }
  }, [cmid]);
  useEffect(() => {
    if (!open) {
      return void 0;
    }
    const selectedIndex = options.findIndex((option) => option.value === current);
    if (selectedIndex <= 0) {
      return void 0;
    }
    let frame = requestAnimationFrame(() => {
      frame = requestAnimationFrame(() => itemRefs.current[selectedIndex]?.focus());
    });
    return () => cancelAnimationFrame(frame);
  }, [open, options, current]);
  const closeMenu = /* @__PURE__ */ __name(() => {
    setOpen(false);
    getTrigger()?.focus();
  }, "closeMenu");
  const handleKeyDown = /* @__PURE__ */ __name((event) => {
    if (event.key === "Tab") {
      event.preventDefault();
      closeMenu();
      return;
    }
    const items = itemRefs.current.filter((item) => item !== null);
    const current2 = items.indexOf(document.activeElement);
    let wrapTo = null;
    if (event.key === "ArrowDown" && current2 === items.length - 1) {
      wrapTo = items[0];
    } else if (event.key === "ArrowUp" && current2 === 0) {
      wrapTo = items[items.length - 1];
    }
    if (wrapTo) {
      event.preventDefault();
      event.stopPropagation();
      wrapTo.focus();
    }
  }, "handleKeyDown");
  const selectOption = /* @__PURE__ */ __name((option) => {
    closeMenu();
    if (!option.mutation) {
      return;
    }
    const previous = current;
    if (option.value !== current) {
      pendingFocus.add(cmid);
      setCurrent(option.value);
    }
    const pending = new Pending(`core_course/VisibilityDropdown:${cmid}`);
    requireAsync("core_courseformat/courseeditor").then((courseEditor) => {
      return courseEditor.getCurrentCourseEditor().dispatch(option.mutation, [cmid]);
    }).catch(() => {
      pendingFocus.delete(cmid);
      setCurrent(previous);
    }).finally(() => {
      pending.resolve();
    });
  }, "selectOption");
  const selected = options.find((option) => option.value === current) ?? options[0];
  const trigger = /* @__PURE__ */ jsxDEV(
    DropdownTrigger,
    {
      className: "activity-visibility-trigger",
      label: selected.triggerlabel,
      appearance: "default",
      size: "md",
      open,
      iconOnly,
      "aria-label": selected.accessiblename,
      "data-visibility": selected.state,
      startIcon: /* @__PURE__ */ jsxDEV(
        "i",
        {
          className: "activity-visibility-icon",
          "aria-hidden": "true",
          dangerouslySetInnerHTML: { __html: selected.icon }
        },
        void 0,
        false,
        {
          fileName: "public/course/js/esm/src/local/cm/VisibilityDropdown.tsx",
          lineNumber: 184,
          columnNumber: 17
        },
        this
      )
    },
    void 0,
    false,
    {
      fileName: "public/course/js/esm/src/local/cm/VisibilityDropdown.tsx",
      lineNumber: 174,
      columnNumber: 9
    },
    this
  );
  return /* @__PURE__ */ jsxDEV(Dropdown, { ref: dropdownRef, trigger, open, onOpenChange: setOpen, placement: "bottom-end", children: /* @__PURE__ */ jsxDEV("div", { role: "none", className: "activity-visibility-menu", onKeyDown: handleKeyDown, children: options.map((option, index) => /* @__PURE__ */ jsxDEV(
    DropdownItemSelect,
    {
      ref: (element) => {
        itemRefs.current[index] = element;
      },
      label: option.label,
      description: option.description,
      selected: option.value === current,
      startIcon: /* @__PURE__ */ jsxDEV(
        "i",
        {
          className: "activity-visibility-option-icon",
          "aria-hidden": "true",
          dangerouslySetInnerHTML: { __html: option.icon }
        },
        void 0,
        false,
        {
          fileName: "public/course/js/esm/src/local/cm/VisibilityDropdown.tsx",
          lineNumber: 206,
          columnNumber: 29
        },
        this
      ),
      "data-value": option.value,
      onClick: () => selectOption(option)
    },
    option.value,
    false,
    {
      fileName: "public/course/js/esm/src/local/cm/VisibilityDropdown.tsx",
      lineNumber: 197,
      columnNumber: 21
    },
    this
  )) }, void 0, false, {
    fileName: "public/course/js/esm/src/local/cm/VisibilityDropdown.tsx",
    lineNumber: 195,
    columnNumber: 13
  }, this) }, void 0, false, {
    fileName: "public/course/js/esm/src/local/cm/VisibilityDropdown.tsx",
    lineNumber: 194,
    columnNumber: 9
  }, this);
}
__name(VisibilityDropdown, "VisibilityDropdown");
export {
  VisibilityDropdown as default
};
//# sourceMappingURL=VisibilityDropdown.dev.js.map
