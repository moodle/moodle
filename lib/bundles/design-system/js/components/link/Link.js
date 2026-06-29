import { forwardRef, isValidElement } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/link/Link.tsx
var allowedVariants = ["primary", "secondary"];
var isIconElement = (el, propName) => {
	return isValidElement(el) && (el.type === "i" || el.type === "svg");
};
var Link = forwardRef(function Link({ label, variant, disabled = false, startIcon, endIcon, className, href, target, rel, onClick, tabIndex, role, ...props }, ref) {
	const resolvedVariant = variant && allowedVariants.includes(variant) ? variant : "primary";
	const resolvedStartIcon = isIconElement(startIcon, "startIcon") ? startIcon : null;
	const resolvedEndIcon = isIconElement(endIcon, "endIcon") ? endIcon : null;
	const handleClick = (event) => {
		if (disabled) {
			event.preventDefault();
			event.stopPropagation();
			return;
		}
		onClick?.(event);
	};
	const resolvedRel = (() => {
		if (target !== "_blank") return rel;
		const parts = new Set([
			...(rel ?? "").split(/\s+/).filter(Boolean),
			"noopener",
			"noreferrer"
		]);
		return Array.from(parts).join(" ");
	})();
	const classes = ["mds-link", `mds-link--${resolvedVariant}`];
	if (disabled) classes.push("mds-link--disabled");
	if (className) classes.push(className);
	return /* @__PURE__ */ jsxs("a", {
		ref,
		...props,
		className: classes.join(" "),
		href: disabled ? void 0 : href,
		target,
		rel: resolvedRel,
		"aria-disabled": disabled || void 0,
		tabIndex: disabled ? -1 : tabIndex,
		role: disabled ? role ?? "link" : role,
		onClick: handleClick,
		children: [
			resolvedStartIcon ? /* @__PURE__ */ jsx("span", {
				className: "mds-link__icon",
				"aria-hidden": "true",
				children: resolvedStartIcon
			}) : null,
			/* @__PURE__ */ jsx("span", {
				className: "mds-link__label",
				children: label
			}),
			resolvedStartIcon ? null : resolvedEndIcon ? /* @__PURE__ */ jsx("span", {
				className: "mds-link__icon",
				"aria-hidden": "true",
				children: resolvedEndIcon
			}) : null
		]
	});
});
Link.displayName = "Link";
//#endregion
export { Link };

//# sourceMappingURL=Link.js.map