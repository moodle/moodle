import { AnchorHTMLAttributes, ButtonHTMLAttributes } from 'react';
import { IconElement } from './dropdownItemUtils';
type DropdownItemActionVariant = 'default' | 'danger';
interface DropdownItemActionCommonProps {
    /** Visible label text. Must be a caller-supplied translated string. */
    label: string;
    /** default (neutral) or danger (destructive) styling. */
    variant?: DropdownItemActionVariant;
    /** Optional leading icon. Accepts only intrinsic `<i>` or `<svg>` elements. */
    startIcon?: IconElement;
    /** Optional secondary line below the label. Caller-supplied translated string. */
    description?: string;
    disabled?: boolean;
    /** Link target (e.g. `_blank`). Only applies when `href` is provided. */
    target?: string;
    /** Link relationship. Only applies when `href` is provided. */
    rel?: string;
}
/** Button mode — activates an action. */
export type DropdownItemActionButtonProps = DropdownItemActionCommonProps & Omit<ButtonHTMLAttributes<HTMLButtonElement>, keyof DropdownItemActionCommonProps | 'children'> & {
    href?: never;
};
/** Link mode — navigates to `href` on activation. Renders as `<a>`. */
export type DropdownItemActionLinkProps = DropdownItemActionCommonProps & Omit<AnchorHTMLAttributes<HTMLAnchorElement>, keyof DropdownItemActionCommonProps | 'children'> & {
    /** When provided, the item renders as an `<a>` element instead of `<button>`. Suppressed when `disabled` is true. */
    href: string;
};
export type DropdownItemActionProps = DropdownItemActionButtonProps | DropdownItemActionLinkProps;
/**
 * Dropdown.item.action — a command row inside a Dropdown menu. Activating it
 * performs an action and typically closes the menu.
 */
export declare const DropdownItemAction: import('react').ForwardRefExoticComponent<DropdownItemActionProps & import('react').RefAttributes<HTMLButtonElement | HTMLAnchorElement>>;
export {};
