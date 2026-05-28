import { jsx } from "react/jsx-runtime";
//#region components/close-button/CloseButton.tsx
var allowedSizes = [
	"sm",
	"md",
	"lg"
];
var CloseButton = ({ "aria-label": ariaLabel, size, className, ...props }) => {
	const classes = [
		"mds-close-button",
		"btn-close",
		`mds-close-button--${size && allowedSizes.includes(size) ? size : "md"}`
	];
	if (className) classes.push(className);
	return /* @__PURE__ */ jsx("button", {
		className: classes.join(" "),
		"aria-label": ariaLabel,
		...props,
		type: "button"
	});
};
//#endregion
export { CloseButton };

//# sourceMappingURL=CloseButton.js.map