import { forwardRef } from "react";
import { jsx } from "react/jsx-runtime";
//#region components/dropdown/DropdownItemDivider.tsx
/** Dropdown.item.divider — a horizontal rule separating groups of items. */
var DropdownItemDivider = forwardRef(function DropdownItemDivider({ className, ...props }, ref) {
	const classes = ["mds-dropdown-divider"];
	if (className) classes.push(className);
	return /* @__PURE__ */ jsx("div", {
		ref,
		role: "separator",
		className: classes.join(" "),
		...props
	});
});
DropdownItemDivider.displayName = "DropdownItemDivider";
//#endregion
export { DropdownItemDivider };

//# sourceMappingURL=DropdownItemDivider.js.map