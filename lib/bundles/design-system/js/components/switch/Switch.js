import { forwardRef, useId } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/switch/Switch.tsx
var allowedVariants = [
	"enable",
	"visibility",
	"lock"
];
var allowedLabelSides = ["end", "start"];
var Switch = forwardRef(({ label, hideLabel = false, variant = "enable", labelSide = "end", className, id: idProp, "aria-label": ariaLabelProp, disabled, ...inputProps }, ref) => {
	const generatedId = useId();
	const id = idProp ?? generatedId;
	const resolvedVariant = variant && allowedVariants.includes(variant) ? variant : "enable";
	const resolvedLabelSide = labelSide && allowedLabelSides.includes(labelSide) ? labelSide : "end";
	const classes = [
		"mds-switch",
		`mds-switch--variant-${resolvedVariant}`,
		`mds-switch--label-${resolvedLabelSide}`,
		hideLabel ? "mds-switch--label-hidden" : ""
	];
	if (className) classes.push(className);
	const nonEmptyAriaLabel = ariaLabelProp?.trim();
	const ariaLabel = hideLabel ? nonEmptyAriaLabel ? ariaLabelProp : label : void 0;
	const shouldRenderVisibleLabel = !hideLabel && !!label;
	return /* @__PURE__ */ jsxs("div", {
		className: classes.filter(Boolean).join(" "),
		children: [/* @__PURE__ */ jsx("input", {
			...inputProps,
			id,
			ref,
			type: "checkbox",
			role: "switch",
			disabled,
			className: "mds-switch-input",
			"aria-label": ariaLabel
		}), /* @__PURE__ */ jsxs("label", {
			className: "mds-switch-control",
			htmlFor: id,
			children: [/* @__PURE__ */ jsx("span", {
				className: "mds-switch-indicator",
				"aria-hidden": "true",
				children: /* @__PURE__ */ jsx("span", {
					className: "mds-switch-focus-ring",
					children: /* @__PURE__ */ jsx("span", {
						className: "mds-switch-track",
						children: /* @__PURE__ */ jsx("span", {
							className: "mds-switch-thumb",
							children: /* @__PURE__ */ jsxs("span", {
								className: "mds-switch-icon",
								children: [/* @__PURE__ */ jsx("span", { className: "mds-switch-icon-item mds-switch-icon-item--unchecked" }), /* @__PURE__ */ jsx("span", { className: "mds-switch-icon-item mds-switch-icon-item--checked" })]
							})
						})
					})
				})
			}), shouldRenderVisibleLabel && /* @__PURE__ */ jsx("span", {
				className: "mds-switch-label",
				children: label
			})]
		})]
	});
});
Switch.displayName = "Switch";
//#endregion
export { Switch };

//# sourceMappingURL=Switch.js.map