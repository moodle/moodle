import { Dropdown } from "../dropdown/Dropdown.js";
import { DropdownItemAction } from "../dropdown/DropdownItemAction.js";
import { Link } from "../link/Link.js";
import { Tooltip } from "../tooltip/Tooltip.js";
import { useEffect, useRef, useState } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
//#region components/breadcrumb/Breadcrumb.tsx
function TruncatingTooltip({ label, placement, labelSelector = ".mds-link__label", children }) {
	const [isTruncated, setIsTruncated] = useState(false);
	const containerRef = useRef(null);
	useEffect(() => {
		const container = containerRef.current;
		if (!container) return;
		let resizeObserver = null;
		const check = () => {
			const labelSpan = container.querySelector(labelSelector);
			if (labelSpan) setIsTruncated(labelSpan.scrollWidth > labelSpan.offsetWidth);
		};
		const connectResizeObserver = () => {
			resizeObserver?.disconnect();
			const labelSpan = container.querySelector(labelSelector);
			if (labelSpan) {
				resizeObserver = new ResizeObserver(check);
				resizeObserver.observe(labelSpan);
			}
		};
		check();
		connectResizeObserver();
		const mutationObserver = new MutationObserver(() => {
			check();
			connectResizeObserver();
		});
		mutationObserver.observe(container, {
			childList: true,
			subtree: true
		});
		return () => {
			resizeObserver?.disconnect();
			mutationObserver.disconnect();
		};
	}, [labelSelector]);
	const content = typeof children === "function" ? children(isTruncated) : children;
	return /* @__PURE__ */ jsx("span", {
		ref: containerRef,
		style: { display: "contents" },
		children: isTruncated ? /* @__PURE__ */ jsx(Tooltip, {
			label,
			placement,
			children: content
		}) : content
	});
}
var Breadcrumb = function Breadcrumb({ items, ariaLabel = "Breadcrumb", overflowAriaLabel = "Show more items", className, ...props }) {
	const [isOverflowOpen, setIsOverflowOpen] = useState(false);
	const showOverflow = Boolean(items && items.length > 4);
	const overflowItems = showOverflow ? items.slice(1, items.length - 3) : [];
	if (!items || items.length < 2) return null;
	const currentItem = items[items.length - 1];
	const visibleMiddleItems = showOverflow ? items.slice(items.length - 3, items.length - 1) : items.slice(1, items.length - 1);
	const classes = ["mds-breadcrumb"];
	if (className) classes.push(className);
	const itemClasses = ["breadcrumb-item"];
	if (items.length === 2) itemClasses.push("items-2");
	if (items.length === 3) itemClasses.push("items-3");
	if (items.length === 4 || showOverflow) itemClasses.push("items-4");
	return /* @__PURE__ */ jsx("nav", {
		"aria-label": ariaLabel,
		className: classes.join(" "),
		...props,
		children: /* @__PURE__ */ jsxs("ol", {
			className: "breadcrumb mds-breadcrumb__list",
			children: [
				/* @__PURE__ */ jsx("li", {
					className: itemClasses.join(" "),
					children: /* @__PURE__ */ jsx(TruncatingTooltip, {
						label: items[0].label,
						placement: "bottom",
						children: /* @__PURE__ */ jsx(Link, {
							href: items[0].href,
							label: items[0].label,
							className: "mds-breadcrumb__link"
						})
					})
				}),
				showOverflow && /* @__PURE__ */ jsx("li", {
					className: itemClasses.concat(["mds-breadcrumb__item--overflow"]).join(" "),
					children: /* @__PURE__ */ jsx(Dropdown, {
						open: isOverflowOpen,
						onOpenChange: setIsOverflowOpen,
						placement: "bottom-start",
						allowPlacementFlip: false,
						trigger: /* @__PURE__ */ jsxs("button", {
							type: "button",
							className: "mds-breadcrumb__overflow-trigger",
							children: [/* @__PURE__ */ jsx("span", {
								className: "visually-hidden",
								children: overflowAriaLabel
							}), /* @__PURE__ */ jsx("span", {
								"aria-hidden": "true",
								children: "…"
							})]
						}),
						children: overflowItems.map((item, index) => /* @__PURE__ */ jsx(DropdownItemAction, {
							href: item.href,
							label: item.label,
							className: "mds-breadcrumb__overflow-link",
							onClick: () => setIsOverflowOpen(false)
						}, index))
					})
				}),
				visibleMiddleItems.map((item, index) => /* @__PURE__ */ jsx("li", {
					className: itemClasses.join(" "),
					children: /* @__PURE__ */ jsx(TruncatingTooltip, {
						label: item.label,
						placement: "bottom",
						children: /* @__PURE__ */ jsx(Link, {
							href: item.href,
							label: item.label,
							className: "mds-breadcrumb__link"
						})
					})
				}, index)),
				/* @__PURE__ */ jsx("li", {
					className: itemClasses.concat("mds-breadcrumb__item--current").join(" "),
					"aria-current": "page",
					children: /* @__PURE__ */ jsx(TruncatingTooltip, {
						label: currentItem.label,
						placement: "bottom",
						labelSelector: ".mds-breadcrumb__label",
						children: (isTruncated) => /* @__PURE__ */ jsx("span", {
							className: "mds-breadcrumb__label",
							tabIndex: isTruncated ? 0 : void 0,
							children: currentItem.label
						})
					})
				})
			]
		})
	});
};
//#endregion
export { Breadcrumb };

//# sourceMappingURL=Breadcrumb.js.map