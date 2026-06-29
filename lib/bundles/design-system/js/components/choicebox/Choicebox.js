import { forwardRef, isValidElement, useId } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/choicebox/Choicebox.tsx
var isIconElement = (el, propName) => {
	return isValidElement(el) && (el.type === "i" || el.type === "svg");
};
var Choicebox = forwardRef(function Choicebox({ label, supportingText, icon, className, id: idProp, "aria-describedby": ariaDescribedByProp, ...inputProps }, ref) {
	const generatedId = useId();
	const id = idProp ?? generatedId;
	const supportingTextId = supportingText ? `${id}-supporting-text` : void 0;
	const ariaDescribedBy = [supportingTextId, ariaDescribedByProp].filter(Boolean).join(" ") || void 0;
	const resolvedIcon = isIconElement(icon, "icon") ? icon : null;
	const wrapperClasses = ["mds-choicebox-wrapper"];
	if (className) wrapperClasses.push(className);
	return /* @__PURE__ */ jsxs("div", {
		className: wrapperClasses.join(" "),
		children: [/* @__PURE__ */ jsx("input", {
			...inputProps,
			type: "radio",
			className: "mds-choicebox-input",
			id,
			ref,
			"aria-describedby": ariaDescribedBy
		}), /* @__PURE__ */ jsxs("label", {
			className: "mds-choicebox",
			htmlFor: id,
			children: [
				resolvedIcon && /* @__PURE__ */ jsx("span", {
					className: "mds-choicebox-icon",
					"aria-hidden": "true",
					children: resolvedIcon
				}),
				/* @__PURE__ */ jsxs("span", {
					className: "mds-choicebox-labels",
					children: [/* @__PURE__ */ jsx("span", {
						className: "mds-choicebox-label",
						children: label
					}), supportingText && /* @__PURE__ */ jsx("span", {
						id: supportingTextId,
						className: "mds-choicebox-supporting-text",
						children: supportingText
					})]
				}),
				/* @__PURE__ */ jsx("span", {
					className: "mds-choicebox-indicator",
					"aria-hidden": "true"
				})
			]
		})]
	});
});
//#endregion
export { Choicebox };

//# sourceMappingURL=Choicebox.js.map