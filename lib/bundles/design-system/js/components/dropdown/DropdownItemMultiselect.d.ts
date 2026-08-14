import { HTMLAttributes } from 'react';
export interface DropdownItemMultiselectProps extends HTMLAttributes<HTMLDivElement> {
    /** Visible label text. Must be a caller-supplied translated string. */
    label: string;
    /** Whether this item is currently checked. Controlled by the consumer. */
    checked?: boolean;
    /** Optional secondary line below the label. Caller-supplied translated string. */
    description?: string;
    /** Prevents interaction and renders the item as unavailable. */
    disabled?: boolean;
}
/**
 * Dropdown.item.multiselect — a multi-select row embedding the Checkbox
 * component as its leading visual indicator. Supports independent
 * checked/unchecked toggling without closing the menu.
 *
 * The outer element carries `role="menuitemcheckbox"` so it participates
 * correctly in the ARIA menu model. The embedded `<Checkbox>` is
 * `aria-hidden` and `tabIndex={-1}` — it is purely visual; the div handles
 * all keyboard interaction and AT announcements.
 */
export declare const DropdownItemMultiselect: import('react').ForwardRefExoticComponent<DropdownItemMultiselectProps & import('react').RefAttributes<HTMLDivElement>>;
