import { forwardRef } from "react";
import { jsx } from "react/jsx-runtime";
//#region components/favourite-button/FavouriteButton.tsx
var FavouriteButton = forwardRef(function FavouriteButton({ selected = false, className, "aria-label": ariaLabel, ...props }, ref) {
	return /* @__PURE__ */ jsx("button", {
		ref,
		className: [
			"mds-favourite-button",
			selected ? "mds-favourite-button--selected" : null,
			className
		].filter(Boolean).join(" "),
		type: "button",
		"aria-label": ariaLabel,
		"aria-pressed": selected,
		...props,
		children: /* @__PURE__ */ jsx("span", {
			className: "mds-favourite-button__icon",
			"aria-hidden": "true"
		})
	});
});
//#endregion
export { FavouriteButton };

//# sourceMappingURL=FavouriteButton.js.map