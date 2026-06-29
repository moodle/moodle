import { InputHTMLAttributes, ReactElement } from 'react';
type IconElement = ReactElement<'i' | 'svg'>;
export interface ChoiceboxProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'type'> {
    /**
     * Required primary label text identifying the option.
     * Keep to 1–5 words for readability.
     */
    label: string;
    /**
     * Optional supporting/descriptive text displayed below the label.
     * Used to add context the label alone cannot convey. Aim for 1–2 lines.
     */
    supportingText?: string;
    /**
     * Optional icon rendered before the label group.
     * Must be an <i> or <svg> element. Use only when the icon adds meaning.
     */
    icon?: IconElement;
}
export declare const Choicebox: import('react').ForwardRefExoticComponent<ChoiceboxProps & import('react').RefAttributes<HTMLInputElement>>;
export {};
