import { forwardRef } from "react";
import { jsx } from "react/jsx-runtime";
//#region components/dropdown/DropdownItemHeader.tsx
/**
* Dropdown.item.header — a non-interactive section label for visual grouping only.
* Excluded from keyboard navigation.
*
* For semantic grouping where AT announces the group name as users navigate
* into it, use `DropdownItemGroup` instead — it wraps its children in a proper
* `role="group"` element satisfying the ARIA ownership contract.
*/
var DropdownItemHeader = forwardRef(function DropdownItemHeader({ label, className, ...props }, ref) {
	const classes = ["mds-dropdown-item", "mds-dropdown-item--header"];
	if (className) classes.push(className);
	return /* @__PURE__ */ jsx("div", {
		ref,
		role: "none",
		className: classes.join(" "),
		...props,
		children: /* @__PURE__ */ jsx("span", {
			className: "mds-dropdown-item__label",
			children: label
		})
	});
});
DropdownItemHeader.displayName = "DropdownItemHeader";
//#endregion
export { DropdownItemHeader };

//# sourceMappingURL=DropdownItemHeader.js.map