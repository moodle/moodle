import { HTMLAttributes } from 'react';
export interface DropdownItemListProps extends HTMLAttributes<HTMLDivElement> {
    /** Visible label text. Must be a caller-supplied translated string. */
    label: string;
    /** Completion state of the item. */
    variant?: 'todo' | 'done';
}
/**
 * Dropdown.item.list — a read-only status row inside a Dropdown menu.
 * Represents a task or option with a binary completion indicator (done/todo).
 * Keyboard-navigable via arrow keys; not activatable (aria-disabled).
 */
export declare const DropdownItemList: import('react').ForwardRefExoticComponent<DropdownItemListProps & import('react').RefAttributes<HTMLDivElement>>;
