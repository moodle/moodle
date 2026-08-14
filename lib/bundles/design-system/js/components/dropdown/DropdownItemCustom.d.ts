import { HTMLAttributes, ReactNode } from 'react';
export interface DropdownItemCustomProps extends HTMLAttributes<HTMLDivElement> {
    /** Arbitrary slot content. The custom item is the escape hatch for item
     *  layouts not covered by the typed variants; interactivity and ARIA for
     *  the content are the consumer's responsibility. */
    children?: ReactNode;
}
/** Dropdown.item.custom — a slot container for bespoke item content. */
export declare const DropdownItemCustom: import('react').ForwardRefExoticComponent<DropdownItemCustomProps & import('react').RefAttributes<HTMLDivElement>>;
