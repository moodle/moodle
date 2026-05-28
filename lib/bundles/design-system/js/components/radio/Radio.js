import { forwardRef, useId } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/radio/Radio.tsx
var Radio = forwardRef(({ invalidFeedback, invalid, className, label, hideLabel = false, ...props }, ref) => {
	const generatedId = useId();
	const id = props.id ?? generatedId;
	const isInvalid = !!invalid;
	const classes = ["mds-form-check"];
	if (!hideLabel) classes.push("form-check");
	if (className) classes.push(className);
	const ariaLabel = hideLabel ? props["aria-label"] ?? label : void 0;
	const feedbackId = invalidFeedback && !hideLabel && invalid ? `${id}-feedback` : void 0;
	return /* @__PURE__ */ jsxs("div", {
		className: classes.join(" "),
		children: [
			/* @__PURE__ */ jsx("input", {
				className: [
					"mds-form-check-input",
					"form-check-input",
					isInvalid ? "is-invalid" : ""
				].filter(Boolean).join(" "),
				type: "radio",
				ref,
				...props,
				"aria-invalid": isInvalid ? true : void 0,
				"aria-label": ariaLabel,
				"aria-describedby": feedbackId,
				id
			}),
			!hideLabel && /* @__PURE__ */ jsx("label", {
				className: "mds-form-check-label form-check-label",
				htmlFor: id,
				children: label
			}),
			feedbackId && /* @__PURE__ */ jsx("div", {
				id: feedbackId,
				className: "mds-form-check-feedback invalid-feedback",
				children: invalidFeedback
			})
		]
	});
});
Radio.displayName = "Radio";
//#endregion
export { Radio };

//# sourceMappingURL=Radio.js.map