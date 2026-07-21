var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
import { Suspense, use } from "react";
import { getString } from "./stringUtils";
function StringInner({ identifier, component, params }) {
  return /* @__PURE__ */ jsxDEV(Fragment, { children: use(getString(identifier, component, params)) }, void 0, false, {
    fileName: "public/lib/js/esm/src/String.tsx",
    lineNumber: 26,
    columnNumber: 12
  }, this);
}
__name(StringInner, "StringInner");
function String({ children, identifier, component = "core", params }) {
  return /* @__PURE__ */ jsxDEV(Suspense, { fallback: children ?? `${identifier}, ${component}`, children: /* @__PURE__ */ jsxDEV(StringInner, { identifier, component, params }, void 0, false, {
    fileName: "public/lib/js/esm/src/String.tsx",
    lineNumber: 32,
    columnNumber: 13
  }, this) }, void 0, false, {
    fileName: "public/lib/js/esm/src/String.tsx",
    lineNumber: 31,
    columnNumber: 9
  }, this);
}
__name(String, "String");
var String_default = String;
export {
  String_default as default
};
//# sourceMappingURL=String.dev.js.map
