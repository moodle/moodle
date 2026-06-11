import { forwardRef, isValidElement } from "react";
import { Fragment, jsx, jsxs } from "react/jsx-runtime";
//#region components/button/Button.tsx
var isIconElement = (el, propName) => {
	return isValidElement(el) && (el.type === "i" || el.type === "svg");
};
var allowedVariants = [
	"primary",
	"secondary",
	"danger",
	"ghost",
	"outline-primary",
	"outline-secondary",
	"outline-danger"
];
var allowedSizes = [
	"sm",
	"md",
	"lg"
];
var Button = forwardRef(function Button({ label, variant, size, startIcon, endIcon, className, type = "button", ...props }, ref) {
	const resolvedVariant = variant && allowedVariants.includes(variant) ? variant : "primary";
	const resolvedSize = size && allowedSizes.includes(size) ? size : "md";
	const resolvedStartIcon = isIconElement(startIcon, "startIcon") ? startIcon : null;
	const resolvedEndIcon = isIconElement(endIcon, "endIcon") ? endIcon : null;
	const isIconOnly = !label && Boolean(resolvedStartIcon || resolvedEndIcon);
	const classes = [
		"mds-btn",
		"btn",
		`btn-${resolvedVariant}`,
		`mds-btn--size-${resolvedSize}`
	];
	if (isIconOnly) classes.push("mds-btn--icon-only");
	if (className) classes.push(className);
	return /* @__PURE__ */ jsx("button", {
		ref,
		className: classes.join(" "),
		type,
		...props,
		children: /* @__PURE__ */ jsxs(Fragment, { children: [
			resolvedStartIcon,
			label,
			resolvedStartIcon ? null : resolvedEndIcon
		] })
	});
});
Button.displayName = "Button";
//#endregion
export { Button };

//# sourceMappingURL=Button.js.map