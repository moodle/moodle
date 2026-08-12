var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * Search input for the Timeline block.
 *
 * Matches the DOM structure of core/search_input_auto (used by the legacy nav-search.mustache).
 *
 * @module     block_timeline/nav/Search
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useRef, useState, useCallback, useEffect, useId } from "react";
import { getString } from "@moodle/lms/core/stringUtils";
const DEBOUNCE_MS = 1e3;
function Search({ onSearch, onSearching }) {
  const uid = useId().replace(/:/g, "");
  const inputId = `searchinput-${uid}`;
  const labelId = `searchinput-label-${uid}`;
  const formId = `searchform-auto-${uid}`;
  const [value, setValue] = useState("");
  const [label, setLabel] = useState("");
  const [clearLabel, setClearLabel] = useState("");
  const timerRef = useRef(null);
  const inputRef = useRef(null);
  useEffect(() => {
    getString("searchevents", "block_timeline").then(setLabel);
    getString("clearsearch", "core").then(setClearLabel);
  }, []);
  const handleChange = useCallback((e) => {
    const next = e.target.value;
    setValue(next);
    onSearching?.(true);
    if (timerRef.current) {
      clearTimeout(timerRef.current);
    }
    timerRef.current = setTimeout(() => {
      onSearching?.(false);
      onSearch(next);
    }, DEBOUNCE_MS);
  }, [onSearch, onSearching]);
  const handleClear = useCallback(() => {
    setValue("");
    if (timerRef.current) {
      clearTimeout(timerRef.current);
    }
    onSearching?.(false);
    onSearch("");
    inputRef.current?.focus();
  }, [onSearch, onSearching]);
  return /* @__PURE__ */ jsxDEV("div", { className: "w-100", children: /* @__PURE__ */ jsxDEV("div", { id: formId, className: "d-flex flex-wrap align-items-center simplesearchform", children: /* @__PURE__ */ jsxDEV("div", { className: "input-group searchbar w-100", role: "search", "aria-labelledby": labelId, children: [
    /* @__PURE__ */ jsxDEV("label", { htmlFor: inputId, id: labelId, children: /* @__PURE__ */ jsxDEV("span", { className: "visually-hidden", children: label }, void 0, false, {
      fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
      lineNumber: 94,
      columnNumber: 25
    }, this) }, void 0, false, {
      fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
      lineNumber: 93,
      columnNumber: 21
    }, this),
    /* @__PURE__ */ jsxDEV(
      "input",
      {
        ref: inputRef,
        type: "text",
        "data-region": "input",
        "data-action": "search",
        id: inputId,
        className: "form-control withclear rounded",
        placeholder: label,
        name: "search",
        value,
        autoComplete: "off",
        onChange: handleChange
      },
      void 0,
      false,
      {
        fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
        lineNumber: 96,
        columnNumber: 21
      },
      this
    ),
    value && /* @__PURE__ */ jsxDEV(
      "button",
      {
        className: "btn btn-clear",
        "data-action": "clearsearch",
        type: "button",
        onClick: handleClear,
        children: [
          /* @__PURE__ */ jsxDEV("i", { className: "icon fa fa-xmark fa-fw", "aria-hidden": "true" }, void 0, false, {
            fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
            lineNumber: 116,
            columnNumber: 29
          }, this),
          /* @__PURE__ */ jsxDEV("span", { className: "visually-hidden", children: clearLabel }, void 0, false, {
            fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
            lineNumber: 117,
            columnNumber: 29
          }, this)
        ]
      },
      void 0,
      true,
      {
        fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
        lineNumber: 110,
        columnNumber: 25
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
    lineNumber: 92,
    columnNumber: 17
  }, this) }, void 0, false, {
    fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
    lineNumber: 91,
    columnNumber: 13
  }, this) }, void 0, false, {
    fileName: "public/blocks/timeline/js/esm/src/nav/Search.tsx",
    lineNumber: 90,
    columnNumber: 9
  }, this);
}
__name(Search, "Search");
export {
  Search as default
};
//# sourceMappingURL=Search.dev.js.map
