import { useDropdownContext } from "./Dropdown.js";
import { isIconElement } from "./dropdownItemUtils.js";
import { forwardRef } from "react";
import { Fragment, jsx, jsxs } from "react/jsx-runtime";
import { useListItem, useMergeRefs } from "@floating-ui/react";
//#region components/dropdown/DropdownItemAction.tsx
var allowedActionVariants = ["default", "danger"];
/**
* Dropdown.item.action — a command row inside a Dropdown menu. Activating it
* performs an action and typically closes the menu.
*/
var DropdownItemAction = forwardRef(function DropdownItemAction({ label, variant = "default", startIcon, description, href, target, rel, className, disabled, onClick, ...restProps }, forwardedRef) {
	const { ref: listItemRef, index } = useListItem({ label });
	const { getItemProps, activeIndex } = useDropdownContext();
	const ref = useMergeRefs([listItemRef, forwardedRef]);
	const resolvedVariant = allowedActionVariants.includes(variant) ? variant : "default";
	const resolvedStartIcon = isIconElement(startIcon) ? startIcon : null;
	const resolvedDescription = resolvedVariant === "danger" ? void 0 : description;
	const isLink = Boolean(href);
	const resolvedRel = target === "_blank" && !rel?.includes("noopener") ? [rel, "noopener"].filter(Boolean).join(" ") : rel;
	const classes = [
		"mds-dropdown-item",
		"mds-dropdown-item--action",
		`mds-dropdown-item--${resolvedVariant}`
	];
	if (resolvedDescription) classes.push("mds-dropdown-item--with-description");
	if (className) classes.push(className);
	const sharedContent = /* @__PURE__ */ jsxs(Fragment, { children: [resolvedStartIcon && /* @__PURE__ */ jsx("span", {
		className: "mds-dropdown-item__icon",
		children: resolvedStartIcon
	}), /* @__PURE__ */ jsxs("span", {
		className: "mds-dropdown-item__label-wrap",
		children: [/* @__PURE__ */ jsx("span", {
			className: "mds-dropdown-item__label",
			children: label
		}), resolvedDescription && /* @__PURE__ */ jsx("span", {
			className: "mds-dropdown-item__description",
			children: resolvedDescription
		})]
	})] });
	if (isLink) return /* @__PURE__ */ jsx("a", {
		ref,
		role: "menuitem",
		href: disabled ? void 0 : href,
		target,
		rel: resolvedRel,
		"aria-disabled": disabled || void 0,
		tabIndex: activeIndex === index ? 0 : -1,
		className: classes.join(" "),
		...getItemProps({
			...restProps,
			onClick: disabled ? (e) => e.preventDefault() : onClick
		}),
		children: sharedContent
	});
	return /* @__PURE__ */ jsx("button", {
		ref,
		type: "button",
		role: "menuitem",
		"aria-disabled": disabled || void 0,
		tabIndex: activeIndex === index ? 0 : -1,
		className: classes.join(" "),
		...getItemProps({
			...restProps,
			onClick: disabled ? (e) => e.preventDefault() : onClick
		}),
		children: sharedContent
	});
});
DropdownItemAction.displayName = "DropdownItemAction";
//#endregion
export { DropdownItemAction };

//# sourceMappingURL=DropdownItemAction.js.map