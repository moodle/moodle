import { ButtonHTMLAttributes } from 'react';
import { IconElement } from './dropdownItemUtils';
export interface DropdownItemSelectProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    /** Visible label text. Must be a caller-supplied translated string. */
    label: string;
    /** Single-select state: shows the surface-subtle fill and trailing check. */
    selected?: boolean;
    /** Optional leading icon. Accepts only intrinsic `<i>` or `<svg>` elements. */
    startIcon?: IconElement;
    /** Optional secondary line below the label. Caller-supplied translated string. */
    description?: string;
}
/**
 * Dropdown.item.select — a single-select option row. Only one item in the
 * group should be selected at a time; selection state is controlled by the
 * consumer (role="menuitemradio" for AT semantics).
 */
export declare const DropdownItemSelect: import('react').ForwardRefExoticComponent<DropdownItemSelectProps & import('react').RefAttributes<HTMLButtonElement>>;
