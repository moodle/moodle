import { DropdownTrigger } from "./DropdownTrigger.js";
import { cloneElement, createContext, forwardRef, isValidElement, useContext, useId, useRef, useState } from "react";
import { jsx, jsxs } from "react/jsx-runtime";
import { FloatingFocusManager, FloatingList, FloatingPortal, autoUpdate, flip, offset, shift, size, useClick, useDismiss, useFloating, useInteractions, useListNavigation, useMergeRefs, useTypeahead } from "@floating-ui/react";
//#region components/dropdown/Dropdown.tsx
var DropdownContext = createContext(null);
var fallbackDropdownContext = {
	getItemProps: (userProps) => userProps ?? {},
	activeIndex: null
};
/**
* Returns the nearest Dropdown's item-interaction helpers.
* Falls back to a passthrough context so items work in isolation (tests /
* standalone story usage).
*/
function useDropdownContext() {
	return useContext(DropdownContext) ?? fallbackDropdownContext;
}
/**
* Dropdown.menu — the panel that hosts Dropdown items.
*
* A passive container: interactive behavior lives on each item. Compose it
* with DropdownItemAction, DropdownItemSelect, DropdownItemExpandable,
* DropdownItemMultiselect, DropdownItemHeader, DropdownItemDivider and
* DropdownItemCustom children.
*/
var DropdownMenu = forwardRef(function DropdownMenu({ className, children, ...props }, ref) {
	const classes = ["mds-dropdown-menu"];
	if (className) classes.push(className);
	return /* @__PURE__ */ jsx("div", {
		ref,
		role: "menu",
		tabIndex: -1,
		className: classes.join(" "),
		...props,
		children
	});
});
DropdownMenu.displayName = "DropdownMenu";
var MENU_OFFSET = 4;
var Dropdown = forwardRef(function Dropdown({ label, variant, appearance, size: triggerSize, startIcon, iconOnly, trigger, open: controlledOpen, defaultOpen = false, onOpenChange, placement = "bottom-start", allowPlacementFlip = true, matchTriggerWidth = false, className, children, ...props }, ref) {
	const [uncontrolledOpen, setUncontrolledOpen] = useState(defaultOpen);
	const open = controlledOpen ?? uncontrolledOpen;
	const [activeIndex, setActiveIndex] = useState(null);
	const elementsRef = useRef([]);
	const labelsRef = useRef([]);
	const triggerId = useId();
	const customTrigger = isValidElement(trigger) ? trigger : null;
	const customTriggerRef = customTrigger?.props.ref;
	const setOpen = (next) => {
		setUncontrolledOpen(next);
		onOpenChange?.(next);
	};
	const { refs, floatingStyles, context } = useFloating({
		open,
		onOpenChange: setOpen,
		placement,
		middleware: [
			offset(MENU_OFFSET),
			...allowPlacementFlip ? [flip()] : [],
			shift({ padding: 8 }),
			...matchTriggerWidth ? [size({ apply({ rects, elements }) {
				Object.assign(elements.floating.style, { minWidth: `${rects.reference.width}px` });
			} })] : []
		],
		whileElementsMounted: autoUpdate
	});
	const { setReference, setFloating } = refs;
	const mergedCustomTriggerRef = useMergeRefs([setReference, customTriggerRef]);
	const menuDirection = refs.reference.current instanceof Element && getComputedStyle(refs.reference.current).direction === "rtl" ? "rtl" : "ltr";
	const click = useClick(context);
	const dismiss = useDismiss(context);
	const listNavigation = useListNavigation(context, {
		listRef: elementsRef,
		activeIndex,
		onNavigate: setActiveIndex
	});
	const typeahead = useTypeahead(context, {
		listRef: labelsRef,
		activeIndex,
		onMatch: setActiveIndex
	});
	const { getReferenceProps, getFloatingProps, getItemProps } = useInteractions([
		click,
		dismiss,
		listNavigation,
		typeahead
	]);
	const classes = ["mds-dropdown"];
	if (className) classes.push(className);
	const renderedTrigger = customTrigger ? cloneElement(customTrigger, {
		ref: mergedCustomTriggerRef,
		id: triggerId,
		"aria-haspopup": "menu",
		"aria-expanded": open,
		...getReferenceProps(customTrigger.props)
	}) : /* @__PURE__ */ jsx(DropdownTrigger, {
		ref: setReference,
		id: triggerId,
		label: label ?? "",
		variant,
		appearance,
		size: triggerSize,
		startIcon,
		iconOnly,
		open,
		...getReferenceProps()
	});
	return /* @__PURE__ */ jsx(DropdownContext.Provider, {
		value: {
			getItemProps,
			activeIndex
		},
		children: /* @__PURE__ */ jsxs("div", {
			ref,
			className: classes.join(" "),
			...props,
			children: [renderedTrigger, open && /* @__PURE__ */ jsx(FloatingPortal, { children: /* @__PURE__ */ jsx(FloatingFocusManager, {
				context,
				modal: false,
				guards: false,
				children: /* @__PURE__ */ jsx(FloatingList, {
					elementsRef,
					labelsRef,
					children: /* @__PURE__ */ jsx(DropdownMenu, {
						ref: setFloating,
						dir: menuDirection,
						style: floatingStyles,
						"aria-labelledby": triggerId,
						...getFloatingProps(),
						children
					})
				})
			}) })]
		})
	});
});
Dropdown.displayName = "Dropdown";
//#endregion
export { Dropdown, DropdownContext, DropdownMenu, useDropdownContext };

//# sourceMappingURL=Dropdown.js.map