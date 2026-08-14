import { forwardRef } from "react";
import { jsx } from "react/jsx-runtime";
//#region components/dropdown/DropdownItemCustom.tsx
/** Dropdown.item.custom — a slot container for bespoke item content. */
var DropdownItemCustom = forwardRef(function DropdownItemCustom({ className, children, ...props }, ref) {
	const classes = ["mds-dropdown-item", "mds-dropdown-item--custom"];
	if (className) classes.push(className);
	return /* @__PURE__ */ jsx("div", {
		ref,
		role: "presentation",
		className: classes.join(" "),
		...props,
		children: /* @__PURE__ */ jsx("span", {
			className: "mds-dropdown-item__slot",
			children
		})
	});
});
DropdownItemCustom.displayName = "DropdownItemCustom";
//#endregion
export { DropdownItemCustom };

//# sourceMappingURL=DropdownItemCustom.js.map