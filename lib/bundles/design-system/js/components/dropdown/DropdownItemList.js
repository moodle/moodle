import { useDropdownContext } from "./Dropdown.js";
import { forwardRef } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
import { useListItem, useMergeRefs } from "@floating-ui/react";
//#region components/dropdown/DropdownItemList.tsx
/**
* Dropdown.item.list — a read-only status row inside a Dropdown menu.
* Represents a task or option with a binary completion indicator (done/todo).
* Keyboard-navigable via arrow keys; not activatable (aria-disabled).
*/
var DropdownItemList = forwardRef(function DropdownItemList({ label, variant = "todo", className, ...props }, forwardedRef) {
	const { ref: listItemRef, index } = useListItem({ label });
	const { getItemProps, activeIndex } = useDropdownContext();
	const ref = useMergeRefs([listItemRef, forwardedRef]);
	const isDone = variant === "done";
	const classes = ["mds-dropdown-item", "mds-dropdown-item--list"];
	if (isDone) classes.push("mds-dropdown-item--selected");
	if (className) classes.push(className);
	return /* @__PURE__ */ jsxs("div", {
		ref,
		role: "menuitemradio",
		"aria-checked": isDone,
		"aria-disabled": "true",
		tabIndex: activeIndex === index ? 0 : -1,
		className: classes.join(" "),
		...props,
		...getItemProps({ onClick: (e) => e.preventDefault() }),
		children: [/* @__PURE__ */ jsx("span", {
			className: "mds-dropdown-item__check",
			"aria-hidden": "true"
		}), /* @__PURE__ */ jsx("span", {
			className: "mds-dropdown-item__label-wrap",
			children: /* @__PURE__ */ jsx("span", {
				className: "mds-dropdown-item__label",
				children: label
			})
		})]
	});
});
DropdownItemList.displayName = "DropdownItemList";
//#endregion
export { DropdownItemList };

//# sourceMappingURL=DropdownItemList.js.map