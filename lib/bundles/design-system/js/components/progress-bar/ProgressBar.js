import { useId } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/progress-bar/ProgressBar.tsx
var allowedStatuses = [
	"in-progress",
	"loading",
	"error",
	"warning"
];
var allowedLabels = [
	"title-and-count",
	"title",
	"inline",
	"none"
];
var ProgressBar = ({ value = 0, min = 0, max = 100, status, labelVariant = "title-and-count", title, count, animated = false, className, ...props }) => {
	const titleId = useId();
	const isAllowedStatus = !!status && allowedStatuses.includes(status);
	const isAllowedLabelVariant = allowedLabels.includes(labelVariant);
	const hasAscendingRange = Number.isFinite(min) && Number.isFinite(max) && min < max;
	const resolvedMin = hasAscendingRange ? min : 0;
	const resolvedMax = hasAscendingRange ? max : 100;
	const resolvedStatus = isAllowedStatus ? status : "in-progress";
	const resolvedLabel = isAllowedLabelVariant ? labelVariant : "title-and-count";
	const clampedValue = Math.min(resolvedMax, Math.max(resolvedMin, value));
	const fillPercent = (clampedValue - resolvedMin) / (resolvedMax - resolvedMin) * 100;
	const visualStatus = fillPercent === 0 ? "empty" : fillPercent === 100 ? "completed" : resolvedStatus;
	const isTitleCount = resolvedLabel === "title-and-count";
	const isTitle = resolvedLabel === "title";
	const isInline = resolvedLabel === "inline";
	const showTitleRow = isTitleCount || isTitle;
	const { "aria-label": ariaLabel, "aria-labelledby": ariaLabelledby, ...restProps } = props;
	const hasVisibleTitle = showTitleRow && Boolean(title);
	const trackLabelledby = ariaLabelledby ?? (hasVisibleTitle && !ariaLabel ? titleId : void 0);
	const trackAriaLabel = ariaLabel ?? (!hasVisibleTitle ? title : void 0);
	const wrapperClasses = [
		"mds-progress-bar",
		`mds-progress-bar--label-${resolvedLabel}`,
		`mds-progress-bar--${visualStatus}`
	];
	if (className) wrapperClasses.push(className);
	const fillClasses = [
		"mds-progress-bar-fill",
		"progress-bar",
		visualStatus === "loading" ? "progress-bar-striped" : "",
		visualStatus === "loading" && animated ? "progress-bar-animated" : ""
	].filter(Boolean).join(" ");
	return /* @__PURE__ */ jsxs("div", {
		className: wrapperClasses.join(" "),
		...restProps,
		children: [
			showTitleRow && /* @__PURE__ */ jsxs("div", {
				className: "mds-progress-bar-label",
				children: [/* @__PURE__ */ jsx("span", {
					id: titleId,
					className: "mds-progress-bar-title",
					children: title
				}), isTitleCount && /* @__PURE__ */ jsx("span", {
					className: "mds-progress-bar-count",
					children: count
				})]
			}),
			/* @__PURE__ */ jsx("div", {
				className: "mds-progress-bar-track progress",
				role: "progressbar",
				"aria-valuenow": clampedValue,
				"aria-valuemin": resolvedMin,
				"aria-valuemax": resolvedMax,
				"aria-labelledby": trackLabelledby,
				"aria-label": trackAriaLabel,
				children: /* @__PURE__ */ jsx("div", {
					className: fillClasses,
					style: { width: `${fillPercent}%` }
				})
			}),
			isInline && /* @__PURE__ */ jsx("span", {
				className: "mds-progress-bar-count",
				children: count
			})
		]
	});
};
//#endregion
export { ProgressBar };

//# sourceMappingURL=ProgressBar.js.map