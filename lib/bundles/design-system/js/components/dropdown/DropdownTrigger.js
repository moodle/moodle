import { Button } from "../button/Button.js";
import { forwardRef, isValidElement } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/dropdown/DropdownTrigger.tsx
var appearanceToButtonVariant = {
	emphasis: "secondary",
	default: "outline-secondary",
	subtle: "ghost"
};
var allowedVariants = ["button", "nav-pill"];
var allowedAppearances = [
	"emphasis",
	"default",
	"subtle"
];
var allowedSizes = ["sm", "md"];
var isIconElement = (el) => isValidElement(el) && (el.type === "i" || el.type === "svg");
/**
* Dropdown.trigger — the clickable affordance that opens a Dropdown menu.
*
* For the button variant, renders a `<Button>` with the chevron passed as
* endIcon alongside any optional startIcon. For the nav-pill variant, renders
* a raw `<button>` with nav-pill CSS classes — NavPill is intentionally not
* reused here because NavPill renders an `<a>` element (navigation semantics),
* whereas a dropdown trigger is a toggle action and must be a `<button>`.
*/
var DropdownTrigger = forwardRef(function DropdownTrigger({ label, variant = "button", appearance = "default", size = "md", startIcon, iconOnly = false, open = false, selected = false, className, type = "button", ...props }, ref) {
	const resolvedVariant = allowedVariants.includes(variant) ? variant : "button";
	const resolvedAppearance = allowedAppearances.includes(appearance) ? appearance : "default";
	const resolvedSize = allowedSizes.includes(size) ? size : "md";
	const resolvedStartIcon = isIconElement(startIcon) ? startIcon : null;
	const isNavPill = resolvedVariant === "nav-pill";
	const isIconOnly = !isNavPill && iconOnly && Boolean(resolvedStartIcon);
	const chevronClasses = ["mds-dropdown-trigger__chevron", resolvedSize === "sm" || isNavPill ? "mds-dropdown-trigger__chevron--sm" : null].filter(Boolean).join(" ");
	if (!isNavPill) {
		const buttonSize = isIconOnly ? "lg" : resolvedSize;
		return /* @__PURE__ */ jsx(Button, {
			ref,
			type,
			variant: appearanceToButtonVariant[resolvedAppearance],
			size: buttonSize,
			label: isIconOnly ? void 0 : label,
			startIcon: resolvedStartIcon ?? void 0,
			endIcon: isIconOnly ? void 0 : /* @__PURE__ */ jsx("i", {
				className: chevronClasses,
				"aria-hidden": "true"
			}),
			"aria-label": isIconOnly ? label : void 0,
			"aria-haspopup": "menu",
			"aria-expanded": open,
			className: [
				"mds-dropdown-trigger",
				open ? "mds-dropdown-trigger--open" : null,
				className
			].filter(Boolean).join(" "),
			...props
		});
	}
	return /* @__PURE__ */ jsxs("button", {
		ref,
		className: [
			"mds-dropdown-trigger",
			"mds-nav-pill",
			"mds-dropdown-trigger--nav-pill",
			open ? "mds-dropdown-trigger--open" : null,
			selected ? "mds-nav-pill--selected" : null,
			className
		].filter(Boolean).join(" "),
		type,
		"aria-haspopup": "menu",
		"aria-expanded": open,
		...props,
		children: [
			selected && /* @__PURE__ */ jsx("span", {
				className: "mds-nav-pill__indicator",
				"aria-hidden": "true"
			}),
			/* @__PURE__ */ jsx("span", {
				className: "mds-nav-pill__label",
				children: label
			}),
			/* @__PURE__ */ jsx("i", {
				className: "mds-dropdown-trigger__chevron mds-dropdown-trigger__chevron--sm",
				"aria-hidden": "true"
			})
		]
	});
});
DropdownTrigger.displayName = "DropdownTrigger";
//#endregion
export { DropdownTrigger };

//# sourceMappingURL=DropdownTrigger.js.map