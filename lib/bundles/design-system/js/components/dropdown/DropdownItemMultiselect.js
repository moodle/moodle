import { useDropdownContext } from "./Dropdown.js";
import { Checkbox } from "../checkbox/Checkbox.js";
import { forwardRef } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
import { useListItem, useMergeRefs } from "@floating-ui/react";
//#region components/dropdown/DropdownItemMultiselect.tsx
/**
* Dropdown.item.multiselect — a multi-select row embedding the Checkbox
* component as its leading visual indicator. Supports independent
* checked/unchecked toggling without closing the menu.
*
* The outer element carries `role="menuitemcheckbox"` so it participates
* correctly in the ARIA menu model. The embedded `<Checkbox>` is
* `aria-hidden` and `tabIndex={-1}` — it is purely visual; the div handles
* all keyboard interaction and AT announcements.
*/
var DropdownItemMultiselect = forwardRef(function DropdownItemMultiselect({ label, checked = false, description, className, disabled, onClick, ...restProps }, forwardedRef) {
	const { ref: listItemRef, index } = useListItem({ label });
	const { getItemProps, activeIndex } = useDropdownContext();
	const ref = useMergeRefs([listItemRef, forwardedRef]);
	const classes = ["mds-dropdown-item", "mds-dropdown-item--multiselect"];
	if (description) classes.push("mds-dropdown-item--with-description");
	if (className) classes.push(className);
	return /* @__PURE__ */ jsxs("div", {
		ref,
		role: "menuitemcheckbox",
		"aria-checked": checked,
		"aria-disabled": disabled || void 0,
		tabIndex: activeIndex === index ? 0 : -1,
		className: classes.join(" "),
		...getItemProps({
			...restProps,
			onClick: disabled ? (e) => e.preventDefault() : onClick,
			onKeyDown: (e) => {
				if (e.key === " ") {
					e.preventDefault();
					if (!disabled) e.currentTarget.click();
				}
				restProps.onKeyDown?.(e);
			}
		}),
		children: [/* @__PURE__ */ jsx("span", {
			inert: true,
			"aria-hidden": "true",
			className: "mds-dropdown-item__checkbox",
			children: /* @__PURE__ */ jsx(Checkbox, {
				hideLabel: true,
				label,
				checked,
				disabled,
				readOnly: true,
				tabIndex: -1
			})
		}), /* @__PURE__ */ jsxs("span", {
			className: "mds-dropdown-item__label-wrap",
			children: [/* @__PURE__ */ jsx("span", {
				className: "mds-dropdown-item__label",
				children: label
			}), description && /* @__PURE__ */ jsx("span", {
				className: "mds-dropdown-item__description",
				children: description
			})]
		})]
	});
});
DropdownItemMultiselect.displayName = "DropdownItemMultiselect";
//#endregion
export { DropdownItemMultiselect };

//# sourceMappingURL=DropdownItemMultiselect.js.map