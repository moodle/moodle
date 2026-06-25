import { AnchorHTMLAttributes } from 'react';
export interface NavPillProps extends Omit<AnchorHTMLAttributes<HTMLAnchorElement>, 'href' | 'aria-current' | 'aria-disabled' | 'role'> {
    /**
     * Visible label text. Must be a caller-supplied translated string.
     */
    label: string;
    /**
     * Whether this pill is currently the active/selected navigation item.
     * Controls the active-indicator dot and selected visual styles.
     */
    selected?: boolean;
    /**
     * Destination URL for the navigation pill.
     */
    href: string;
    /**
     * Marks the anchor as non-interactive while preserving anchor semantics.
     */
    disabled?: boolean;
}
export declare const NavPill: import('react').ForwardRefExoticComponent<NavPillProps & import('react').RefAttributes<HTMLAnchorElement>>;
