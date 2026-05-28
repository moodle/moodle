import { isValidElement } from "react";
import { jsxs } from "react/jsx-runtime";
//#region components/badge/Badge.tsx
var isIconElement = (el, propName) => {
	return isValidElement(el) && (el.type === "i" || el.type === "svg");
};
var allowedVariants = [
	"primary",
	"secondary",
	"success",
	"danger",
	"warning",
	"info"
];
var Badge = ({ label, variant, subtle = false, pill = false, startIcon, endIcon, className, ...props }) => {
	const resolvedVariant = variant && allowedVariants.includes(variant) ? variant : "primary";
	const resolvedStartIcon = isIconElement(startIcon, "startIcon") ? startIcon : null;
	let resolvedEndIcon = isIconElement(endIcon, "endIcon") ? endIcon : null;
	if (resolvedStartIcon && resolvedEndIcon) resolvedEndIcon = null;
	const classes = [
		"mds-badge",
		"badge",
		`mds-badge--${resolvedVariant}`
	];
	if (resolvedStartIcon || resolvedEndIcon) classes.push("mds-badge--has-icon");
	if (subtle) classes.push("mds-badge--subtle");
	if (pill) classes.push("mds-badge--pill");
	if (className) classes.push(className);
	return /* @__PURE__ */ jsxs("span", {
		className: classes.join(" "),
		...props,
		children: [
			resolvedStartIcon,
			label,
			resolvedEndIcon
		]
	});
};
//#endregion
export { Badge };

//# sourceMappingURL=Badge.js.map