import { DropdownContext, DropdownMenu, useDropdownContext } from "./Dropdown.js";
import { forwardRef, useId, useLayoutEffect, useRef, useState } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
import { FloatingFocusManager, FloatingList, autoUpdate, flip, offset, shift, useDismiss, useFloating, useInteractions, useListItem, useListNavigation, useMergeRefs, useTypeahead } from "@floating-ui/react";
import { createPortal } from "react-dom";
//#region components/dropdown/DropdownItemExpandable.tsx
var SUBMENU_MAIN_OFFSET = 8;
var SUBMENU_CROSS_OFFSET_START = -5;
var SUBMENU_CROSS_OFFSET_END = 5;
/**
* Dropdown.item.expandable — a parent row that opens a nested Dropdown menu.
*
* Submenu positioning is delegated to `@floating-ui/react` (`placement='right-start'`
* with `flip` and `shift` middleware), which replaces the previous manual
* getBoundingClientRect / scroll-resize-listener approach and handles RTL direction
* automatically. The submenu is portaled via createPortal (react-dom) to avoid overflow clipping
* and to keep the inline DOM clean — FloatingPortal is intentionally not used here
* because it renders an inline span[aria-owns] sibling that would land inside the
* parent role="menu" element and trigger aria-required-children violations.
*/
var DropdownItemExpandable = forwardRef(function DropdownItemExpandable({ label, children, open: controlledOpen, defaultOpen = false, onOpenChange, placement = "right-start", onClick, className, disabled, ...restProps }, forwardedRef) {
	const { ref: listItemRef, index } = useListItem({ label });
	const { getItemProps: parentGetItemProps, activeIndex: parentActiveIndex } = useDropdownContext();
	const labelId = useId();
	const hasSubmenu = children != null;
	const [uncontrolledOpen, setUncontrolledOpen] = useState(defaultOpen);
	const isOpen = hasSubmenu && !disabled && (controlledOpen ?? uncontrolledOpen);
	const [submenuActiveIndex, setSubmenuActiveIndex] = useState(null);
	const submenuElementsRef = useRef([]);
	const submenuLabelsRef = useRef([]);
	const setOpen = (next) => {
		setUncontrolledOpen(next);
		onOpenChange?.(next);
	};
	const [submenuDirection, setSubmenuDirection] = useState("ltr");
	const effectivePlacement = submenuDirection === "rtl" ? placement.startsWith("right") ? placement.replace("right", "left") : placement.startsWith("left") ? placement.replace("left", "right") : placement : placement;
	const submenuCrossAxisOffset = effectivePlacement.endsWith("end") ? SUBMENU_CROSS_OFFSET_END : SUBMENU_CROSS_OFFSET_START;
	const { refs: submenuRefs, floatingStyles: submenuFloatingStyles, context: submenuContext } = useFloating({
		open: isOpen,
		onOpenChange: setOpen,
		placement: effectivePlacement,
		middleware: [
			offset({
				mainAxis: SUBMENU_MAIN_OFFSET,
				crossAxis: submenuCrossAxisOffset
			}),
			flip(),
			shift({ padding: 8 })
		],
		whileElementsMounted: autoUpdate
	});
	useLayoutEffect(() => {
		const el = submenuRefs.reference.current;
		if (el instanceof Element) {
			const direction = getComputedStyle(el).direction === "rtl" ? "rtl" : "ltr";
			setSubmenuDirection(direction);
		}
	}, [submenuRefs.reference]);
	const dismiss = useDismiss(submenuContext);
	const submenuListNavigation = useListNavigation(submenuContext, {
		listRef: submenuElementsRef,
		activeIndex: submenuActiveIndex,
		onNavigate: setSubmenuActiveIndex
	});
	const submenuTypeahead = useTypeahead(submenuContext, {
		listRef: submenuLabelsRef,
		activeIndex: submenuActiveIndex,
		onMatch: setSubmenuActiveIndex
	});
	const { getFloatingProps, getItemProps: getSubmenuItemProps } = useInteractions([
		dismiss,
		submenuListNavigation,
		submenuTypeahead
	]);
	const ref = useMergeRefs([
		forwardedRef,
		listItemRef,
		submenuRefs.setReference
	]);
	const classes = ["mds-dropdown-item", "mds-dropdown-item--expandable"];
	if (className) classes.push(className);
	return /* @__PURE__ */ jsxs(DropdownContext.Provider, {
		value: {
			getItemProps: getSubmenuItemProps,
			activeIndex: submenuActiveIndex
		},
		children: [/* @__PURE__ */ jsxs("button", {
			ref,
			type: "button",
			role: "menuitem",
			"aria-disabled": disabled || void 0,
			className: classes.join(" "),
			tabIndex: parentActiveIndex === index ? 0 : -1,
			...restProps,
			...parentGetItemProps({
				onClick: (e) => {
					if (disabled) {
						e.preventDefault();
						return;
					}
					if (hasSubmenu) setOpen(!isOpen);
					onClick?.(e);
				},
				onKeyDown: (e) => {
					if (!hasSubmenu || isOpen) return;
					const openKey = getComputedStyle(e.currentTarget).direction === "rtl" ? "ArrowLeft" : "ArrowRight";
					if (e.key === openKey) {
						e.preventDefault();
						e.stopPropagation();
						setOpen(true);
					}
				}
			}),
			...hasSubmenu ? {
				"aria-haspopup": "menu",
				"aria-expanded": isOpen
			} : {},
			children: [/* @__PURE__ */ jsx("span", {
				className: "mds-dropdown-item__label-wrap",
				children: /* @__PURE__ */ jsx("span", {
					id: labelId,
					className: "mds-dropdown-item__label",
					children: label
				})
			}), hasSubmenu && /* @__PURE__ */ jsx("span", {
				className: "mds-dropdown-item__chevron-right",
				"aria-hidden": "true"
			})]
		}), isOpen && createPortal(/* @__PURE__ */ jsx(FloatingFocusManager, {
			context: submenuContext,
			modal: false,
			guards: false,
			children: /* @__PURE__ */ jsx(FloatingList, {
				elementsRef: submenuElementsRef,
				labelsRef: submenuLabelsRef,
				children: /* @__PURE__ */ jsx(DropdownMenu, {
					ref: submenuRefs.setFloating,
					dir: submenuDirection,
					style: submenuFloatingStyles,
					"aria-labelledby": labelId,
					...getFloatingProps({ onKeyDown(e) {
						const refEl = submenuContext.refs.domReference.current;
						const closeKey = getComputedStyle(refEl ?? e.currentTarget).direction === "rtl" ? "ArrowRight" : "ArrowLeft";
						if (e.key === closeKey) {
							e.preventDefault();
							e.stopPropagation();
							setOpen(false);
						}
					} }),
					children
				})
			})
		}), document.body)]
	});
});
DropdownItemExpandable.displayName = "DropdownItemExpandable";
//#endregion
export { DropdownItemExpandable };

//# sourceMappingURL=DropdownItemExpandable.js.map