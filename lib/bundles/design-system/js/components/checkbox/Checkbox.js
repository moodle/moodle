import { forwardRef, useEffect, useId, useRef } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/checkbox/Checkbox.tsx
var Checkbox = forwardRef(({ invalidFeedback, invalid, indeterminate = false, supportingText, className, label, hideLabel = false, id: idProp, required, "aria-label": ariaLabelProp, ...inputProps }, ref) => {
	const generatedId = useId();
	const id = idProp ?? generatedId;
	const hasVisibleLabel = !hideLabel;
	const isInvalid = !!invalid;
	const isIndeterminate = !!indeterminate;
	const inputRef = useRef(null);
	useEffect(() => {
		if (inputRef.current) inputRef.current.indeterminate = isIndeterminate;
	}, [isIndeterminate]);
	const classes = ["mds-checkbox"];
	if (hasVisibleLabel) classes.push("form-check");
	if (className) classes.push(className);
	const ariaLabel = hideLabel ? ariaLabelProp ?? label : void 0;
	const messageText = hasVisibleLabel ? isInvalid && invalidFeedback ? invalidFeedback : supportingText : void 0;
	const hasInvalidFeedback = hasVisibleLabel && isInvalid && !!invalidFeedback;
	const feedbackId = messageText ? `${id}-feedback` : void 0;
	return /* @__PURE__ */ jsxs("div", {
		className: classes.join(" "),
		children: [
			/* @__PURE__ */ jsx("input", {
				className: [
					"mds-checkbox-input",
					"form-check-input",
					isInvalid ? "is-invalid" : ""
				].filter(Boolean).join(" "),
				ref: (node) => {
					inputRef.current = node;
					if (typeof ref === "function") ref(node);
					else if (ref) ref.current = node;
				},
				...inputProps,
				type: "checkbox",
				required,
				"aria-invalid": isInvalid ? true : void 0,
				"aria-label": ariaLabel,
				"aria-checked": isIndeterminate ? "mixed" : void 0,
				"aria-describedby": feedbackId,
				id
			}),
			hasVisibleLabel && /* @__PURE__ */ jsxs("label", {
				className: "mds-checkbox-label form-check-label",
				htmlFor: id,
				children: [/* @__PURE__ */ jsx("span", {
					className: "mds-checkbox-label-text",
					children: label
				}), required && /* @__PURE__ */ jsx("span", {
					className: "mds-checkbox-required",
					"aria-hidden": "true",
					children: "*"
				})]
			}),
			feedbackId && /* @__PURE__ */ jsx("div", {
				id: feedbackId,
				className: ["mds-checkbox-feedback", hasInvalidFeedback ? "invalid-feedback" : "mds-checkbox-supporting-text"].filter(Boolean).join(" "),
				children: messageText
			})
		]
	});
});
Checkbox.displayName = "Checkbox";
//#endregion
export { Checkbox };

//# sourceMappingURL=Checkbox.js.map