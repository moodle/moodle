import { forwardRef } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/nav-pill/NavPill.tsx
var NavPill = forwardRef(function NavPill({ label, selected = false, className, disabled, onClick, href, target, rel, tabIndex, ...props }, ref) {
	const isDisabled = selected ? false : disabled;
	const classes = [
		"mds-nav-pill",
		selected ? "mds-nav-pill--selected" : null,
		className
	].filter(Boolean).join(" ");
	const resolvedHref = isDisabled ? void 0 : href;
	const resolvedRel = (() => {
		if (target !== "_blank") return rel;
		const parts = new Set([
			...(rel ?? "").split(/\s+/).filter(Boolean),
			"noopener",
			"noreferrer"
		]);
		return Array.from(parts).join(" ");
	})();
	const handleClick = (event) => {
		if (isDisabled) {
			event.preventDefault();
			event.stopPropagation();
			return;
		}
		onClick?.(event);
	};
	return /* @__PURE__ */ jsxs("a", {
		ref,
		...props,
		className: classes,
		target,
		rel: resolvedRel,
		href: resolvedHref,
		"aria-disabled": isDisabled ? "true" : void 0,
		tabIndex: isDisabled ? -1 : tabIndex,
		role: isDisabled ? "link" : void 0,
		onClick: handleClick,
		"aria-current": selected ? "page" : void 0,
		children: [selected && /* @__PURE__ */ jsx("span", {
			className: "mds-nav-pill__indicator",
			"aria-hidden": "true"
		}), /* @__PURE__ */ jsx("span", {
			className: "mds-nav-pill__label",
			children: label
		})]
	});
});
NavPill.displayName = "NavPill";
//#endregion
export { NavPill };

//# sourceMappingURL=NavPill.js.map