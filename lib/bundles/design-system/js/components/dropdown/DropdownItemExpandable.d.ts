import { Placement } from '@floating-ui/react';
import { ButtonHTMLAttributes, ReactNode } from 'react';
export interface DropdownItemExpandableProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    /** Visible label text. Must be a caller-supplied translated string. */
    label: string;
    /** Submenu content (Dropdown item components). Presence enables the
     *  expand behavior; without children the row renders inert chrome only. */
    children?: ReactNode;
    /** Controlled submenu open state. */
    open?: boolean;
    defaultOpen?: boolean;
    onOpenChange?: (open: boolean) => void;
    /** Submenu placement relative to the expandable row. Defaults to 'right-start'.
     *  flip() will invert to the opposite side when space is insufficient. */
    placement?: Placement;
}
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
export declare const DropdownItemExpandable: import('react').ForwardRefExoticComponent<DropdownItemExpandableProps & import('react').RefAttributes<HTMLButtonElement>>;
