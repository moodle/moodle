import { ButtonHTMLAttributes, ReactElement } from 'react';
type DropdownTriggerVariant = 'button' | 'nav-pill';
type DropdownTriggerAppearance = 'emphasis' | 'default' | 'subtle';
type DropdownTriggerSize = 'sm' | 'md';
type IconElement = ReactElement<'i' | 'svg'>;
export interface DropdownTriggerProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    /** Visible label text. Must be a caller-supplied translated string.
     *  For icon-only triggers it becomes the aria-label instead. */
    label: string;
    /** Visual form: a standalone button or a navigation pill. The nav-pill
     *  variant is constrained to Appearance=default and Size=md per the design. */
    variant?: DropdownTriggerVariant;
    /** emphasis = filled secondary, default = outlined secondary, subtle = ghost. */
    appearance?: DropdownTriggerAppearance;
    /** Visual size: md (default) or sm. Nav-pill variant is constrained to md. */
    size?: DropdownTriggerSize;
    /** Optional leading icon. Accepts only intrinsic `<i>` or `<svg>` elements. */
    startIcon?: IconElement;
    /** Renders only the startIcon (no label text, no chevron); the label prop is
     *  applied as aria-label so the trigger keeps an accessible name. */
    iconOnly?: boolean;
    /** Whether the dropdown this trigger controls is open. Drives aria-expanded
     *  and the pressed/active visual state (Figma State=active). */
    open?: boolean;
    /** Marks the nav-pill trigger as selected — indicates that a child destination
     *  in the dropdown menu is the currently active page. Only applies when
     *  variant='nav-pill'; ignored (with a dev warning) on the button variant. */
    selected?: boolean;
}
/**
 * Dropdown.trigger — the clickable affordance that opens a Dropdown menu.
 *
 * For the button variant, renders a `<Button>` with the chevron passed as
 * endIcon alongside any optional startIcon. For the nav-pill variant, renders
 * a raw `<button>` with nav-pill CSS classes — NavPill is intentionally not
 * reused here because NavPill renders an `<a>` element (navigation semantics),
 * whereas a dropdown trigger is a toggle action and must be a `<button>`.
 */
export declare const DropdownTrigger: import('react').ForwardRefExoticComponent<DropdownTriggerProps & import('react').RefAttributes<HTMLButtonElement>>;
export {};
