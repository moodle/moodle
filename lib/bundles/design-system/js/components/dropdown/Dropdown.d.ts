import { Placement } from '@floating-ui/react';
import { HTMLAttributes, ReactElement, ReactNode, RefAttributes } from 'react';
import { DropdownTriggerProps } from './DropdownTrigger';
type IconElement = ReactElement<'i' | 'svg'>;
type CustomTriggerProps = React.HTMLProps<HTMLElement> & RefAttributes<HTMLElement>;
type CustomTriggerElement = ReactElement<CustomTriggerProps>;
export interface DropdownContextValue {
    /** Merge Floating UI item-interaction props with the caller's own props. */
    getItemProps: (userProps?: React.HTMLProps<HTMLElement>) => Record<string, unknown>;
    /** Index of the currently keyboard-active item, or null on first open before
     *  any arrow-key movement. Items set their tabIndex based on this value. */
    activeIndex: number | null;
}
export declare const DropdownContext: import('react').Context<DropdownContextValue | null>;
/**
 * Returns the nearest Dropdown's item-interaction helpers.
 * Falls back to a passthrough context so items work in isolation (tests /
 * standalone story usage).
 */
export declare function useDropdownContext(): DropdownContextValue;
export type DropdownMenuProps = HTMLAttributes<HTMLDivElement>;
/**
 * Dropdown.menu — the panel that hosts Dropdown items.
 *
 * A passive container: interactive behavior lives on each item. Compose it
 * with DropdownItemAction, DropdownItemSelect, DropdownItemExpandable,
 * DropdownItemMultiselect, DropdownItemHeader, DropdownItemDivider and
 * DropdownItemCustom children.
 */
export declare const DropdownMenu: import('react').ForwardRefExoticComponent<DropdownMenuProps & RefAttributes<HTMLDivElement>>;
export interface DropdownProps extends HTMLAttributes<HTMLDivElement> {
    /** Trigger label. Must be a caller-supplied translated string. Required
     *  unless `trigger` is provided. */
    label?: string;
    /** Trigger form — see DropdownTrigger. Ignored when `trigger` is provided. */
    variant?: DropdownTriggerProps['variant'];
    /** Trigger appearance — see DropdownTrigger. Ignored when `trigger` is provided. */
    appearance?: DropdownTriggerProps['appearance'];
    /** Trigger size — see DropdownTrigger. Ignored when `trigger` is provided. */
    size?: DropdownTriggerProps['size'];
    /** Optional leading trigger icon. Accepts only intrinsic `<i>` or `<svg>` elements.
     *  Ignored when `trigger` is provided. */
    startIcon?: IconElement;
    /** Renders an icon-only trigger; the label becomes its aria-label.
     *  Ignored when `trigger` is provided. */
    iconOnly?: boolean;
    /**
     * Escape hatch for a fully custom trigger element when none of the built-in
     * DropdownTrigger visual variants fit (e.g. a link-styled trigger). The
     * element is cloned with the floating reference ref plus the ARIA and
     * interaction props Dropdown needs — it must forward its `ref` to a
     * focusable host element. When provided, `label`, `variant`, `appearance`,
     * `size`, `startIcon` and `iconOnly` are ignored.
     */
    trigger?: CustomTriggerElement;
    /** Controlled open state; leave undefined for uncontrolled behavior. */
    open?: boolean;
    defaultOpen?: boolean;
    onOpenChange?: (open: boolean) => void;
    /** Menu placement relative to the trigger. Defaults to 'bottom-start'.
     *  flip() will invert to the opposite side when space is insufficient. */
    placement?: Placement;
    /** Whether the menu may flip to an alternate placement when space is
     *  insufficient. Defaults to true. */
    allowPlacementFlip?: boolean;
    /** When true, the menu panel grows to be at least as wide as the trigger.
     *  Useful when the trigger label is wider than the default 217px minimum. */
    matchTriggerWidth?: boolean;
    /** Menu content (Dropdown item components). */
    children?: ReactNode;
}
export declare const Dropdown: import('react').ForwardRefExoticComponent<DropdownProps & RefAttributes<HTMLDivElement>>;
export {};
