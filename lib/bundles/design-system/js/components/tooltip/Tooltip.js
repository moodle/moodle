import { cloneElement, isValidElement, useEffect, useState } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
import { FloatingArrow, FloatingPortal, arrow, autoUpdate, flip, limitShift, offset, shift, useClick, useDismiss, useFloating, useFocus, useHover, useInteractions, useMergeRefs, useRole } from "@floating-ui/react";
//#region components/tooltip/Tooltip.tsx
var allowedPlacements = [
	"top",
	"bottom",
	"left",
	"right"
];
var allowedVariants = ["dark", "light"];
var hasTextChildren = (value) => {
	if (typeof value === "string") return value.trim().length > 0;
	if (typeof value === "number") return true;
	if (Array.isArray(value)) return value.some(hasTextChildren);
	return false;
};
var ARROW_HEIGHT = 6;
var ARROW_WIDTH = 12;
var Tooltip = ({ label, placement = "top", variant = "dark", children, className, ...props }) => {
	const { "data-forced-open": dataForcedOpen, ...wrapperProps } = props;
	const [isOpen, setIsOpen] = useState(false);
	const isForcedOpen = dataForcedOpen !== void 0;
	const isTooltipOpen = isForcedOpen || isOpen;
	const [arrowEl, setArrowEl] = useState(null);
	useEffect(() => {}, [placement, variant]);
	const resolvedPlacement = allowedPlacements.includes(placement) ? placement : "top";
	const resolvedVariant = allowedVariants.includes(variant) ? variant : "dark";
	const { refs, floatingStyles, context, isPositioned, update } = useFloating({
		open: isTooltipOpen,
		onOpenChange: (nextOpen) => {
			if (!isForcedOpen) setIsOpen(nextOpen);
		},
		placement: resolvedPlacement,
		middleware: [
			offset(14),
			flip(),
			shift({
				padding: 8,
				limiter: limitShift()
			}),
			arrow({
				element: arrowEl,
				padding: 4
			})
		],
		whileElementsMounted: isTooltipOpen ? autoUpdate : void 0
	});
	const { setReference, setFloating } = refs;
	useEffect(() => {
		if (isTooltipOpen) update();
	}, [isTooltipOpen, update]);
	const hover = useHover(context, {
		move: false,
		delay: {
			open: 200,
			close: 0
		}
	});
	const focus = useFocus(context);
	const click = useClick(context, { ignoreMouse: true });
	const dismiss = useDismiss(context, { outsidePress: true });
	const role = useRole(context, { role: "tooltip" });
	const { getReferenceProps, getFloatingProps } = useInteractions([
		hover,
		focus,
		click,
		dismiss,
		role
	]);
	const wrapperClassName = [
		"mds-tooltip",
		`mds-tooltip--${resolvedVariant}`,
		className
	].filter(Boolean).join(" ");
	const triggerElement = isValidElement(children) ? children : null;
	const childRef = triggerElement?.props.ref;
	const mergedTriggerRef = useMergeRefs([setReference, childRef]);
	if (!triggerElement) return /* @__PURE__ */ jsx("div", {
		className: wrapperClassName,
		...props
	});
	const existingDescribedBy = triggerElement.props["aria-describedby"] ?? "";
	const describedByIds = /* @__PURE__ */ new Set([...existingDescribedBy.split(/\s+/).filter(Boolean), context.floatingId]);
	const existingAriaLabel = triggerElement.props["aria-label"];
	const resolvedAriaStrategy = Boolean(existingAriaLabel) || Boolean(triggerElement.props["aria-labelledby"]) || Boolean(triggerElement.props.label?.trim()) || hasTextChildren(triggerElement.props.children) ? "description" : "label";
	const trigger = cloneElement(triggerElement, {
		...getReferenceProps(triggerElement.props),
		...resolvedAriaStrategy === "description" ? { "aria-describedby": Array.from(describedByIds).join(" ") } : { "aria-label": existingAriaLabel ?? label },
		ref: mergedTriggerRef
	});
	return /* @__PURE__ */ jsxs("div", {
		className: wrapperClassName,
		...wrapperProps,
		children: [trigger, /* @__PURE__ */ jsx(FloatingPortal, { children: /* @__PURE__ */ jsxs("div", {
			ref: setFloating,
			style: floatingStyles,
			className: ["mds-tooltip__bubble", `mds-tooltip__bubble--${resolvedVariant}`].join(" "),
			id: context.floatingId,
			"data-open": isTooltipOpen ? "" : void 0,
			"data-positioned": isTooltipOpen && isPositioned ? "" : void 0,
			"data-forced-open": dataForcedOpen,
			...getFloatingProps(),
			children: [/* @__PURE__ */ jsx("div", {
				className: "mds-tooltip__content",
				children: /* @__PURE__ */ jsx("span", {
					className: "mds-tooltip__text",
					children: label
				})
			}), /* @__PURE__ */ jsx(FloatingArrow, {
				ref: setArrowEl,
				context,
				width: ARROW_WIDTH,
				height: ARROW_HEIGHT,
				className: "mds-tooltip__arrow"
			})]
		}) })]
	});
};
//#endregion
export { Tooltip };

//# sourceMappingURL=Tooltip.js.map