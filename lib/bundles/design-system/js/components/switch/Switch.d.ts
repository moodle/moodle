import { InputHTMLAttributes } from 'react';
export type SwitchVariant = 'enable' | 'visibility' | 'lock';
export type SwitchLabelSide = 'end' | 'start';
export interface SwitchProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'type'> {
    /** Visible label text. When hideLabel is true this also serves as the aria-label fallback
     *  if no explicit aria-label prop is provided. */
    label?: string;
    /** When true, hides the visible label text while preserving an accessible name on the input. */
    hideLabel?: boolean;
    /** Semantic variant that controls the thumb icon set. */
    variant?: SwitchVariant;
    /** Places the label after (end) or before (start) the switch indicator. */
    labelSide?: SwitchLabelSide;
}
export declare const Switch: import('react').ForwardRefExoticComponent<SwitchProps & import('react').RefAttributes<HTMLInputElement>>;
