import { HTMLAttributes } from 'react';
export interface DropdownItemHeaderProps extends HTMLAttributes<HTMLDivElement> {
    /** Visible label text. Must be a caller-supplied translated string. */
    label: string;
}
/**
 * Dropdown.item.header — a non-interactive section label for visual grouping only.
 * Excluded from keyboard navigation.
 *
 * For semantic grouping where AT announces the group name as users navigate
 * into it, use `DropdownItemGroup` instead — it wraps its children in a proper
 * `role="group"` element satisfying the ARIA ownership contract.
 */
export declare const DropdownItemHeader: import('react').ForwardRefExoticComponent<DropdownItemHeaderProps & import('react').RefAttributes<HTMLDivElement>>;
