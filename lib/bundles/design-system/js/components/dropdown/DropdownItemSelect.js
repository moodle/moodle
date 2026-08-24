import { useDropdownContext } from "./Dropdown.js";
import { isIconElement } from "./dropdownItemUtils.js";
import { forwardRef } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
import { useListItem, useMergeRefs } from "@floating-ui/react";
//#region components/dropdown/DropdownItemSelect.tsx
/**
* Dropdown.item.select — a single-select option row. Only one item in the
* group should be selected at a time; selection state is controlled by the
* consumer (role="menuitemradio" for AT semantics).
*/
var DropdownItemSelect = forwardRef(function DropdownItemSelect({ label, selected = false, startIcon, description, className, disabled, onClick, ...restProps }, forwardedRef) {
	const { ref: listItemRef, index } = useListItem({ label });
	const { getItemProps, activeIndex } = useDropdownContext();
	const ref = useMergeRefs([listItemRef, forwardedRef]);
	const resolvedStartIcon = isIconElement(startIcon) ? startIcon : null;
	const classes = ["mds-dropdown-item", "mds-dropdown-item--select"];
	if (selected) classes.push("mds-dropdown-item--selected");
	if (description) classes.push("mds-dropdown-item--with-description");
	if (className) classes.push(className);
	return /* @__PURE__ */ jsxs("button", {
		ref,
		type: "button",
		role: "menuitemradio",
		"aria-checked": selected,
		"aria-disabled": disabled || void 0,
		tabIndex: activeIndex === index ? 0 : -1,
		className: classes.join(" "),
		...getItemProps({
			...restProps,
			onClick: disabled ? (e) => e.preventDefault() : onClick
		}),
		children: [
			resolvedStartIcon && /* @__PURE__ */ jsx("span", {
				className: "mds-dropdown-item__icon",
				children: resolvedStartIcon
			}),
			/* @__PURE__ */ jsxs("span", {
				className: "mds-dropdown-item__label-wrap",
				children: [/* @__PURE__ */ jsx("span", {
					className: "mds-dropdown-item__label",
					children: label
				}), description && /* @__PURE__ */ jsx("span", {
					className: "mds-dropdown-item__description",
					children: description
				})]
			}),
			/* @__PURE__ */ jsx("span", {
				className: "mds-dropdown-item__check-wrap",
				"aria-hidden": "true",
				children: /* @__PURE__ */ jsx("span", { className: "mds-dropdown-item__check" })
			})
		]
	});
});
DropdownItemSelect.displayName = "DropdownItemSelect";
//#endregion
export { DropdownItemSelect };

//# sourceMappingURL=DropdownItemSelect.js.map