import { HTMLAttributes, ReactElement } from 'react';
type BadgeVariant = 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info';
type IconElement = ReactElement<'i' | 'svg'>;
export interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
    /** Visible badge text. Must be a caller-supplied translated string. */
    label: string;
    /** Colour/semantic variant. Defaults to `primary`. */
    variant?: BadgeVariant;
    /** When true, renders the low-contrast (subtle) style with a light background and border. */
    subtle?: boolean;
    /** When true, renders fully rounded pill shape instead of the default slight rounding. */
    pill?: boolean;
    /** Optional icon rendered before the label. Must be an `<i>` or `<svg>` element. Mutually exclusive with `endIcon`. */
    startIcon?: IconElement;
    /** Optional icon rendered after the label. Must be an `<i>` or `<svg>` element. Mutually exclusive with `startIcon`. */
    endIcon?: IconElement;
}
export declare const Badge: ({ label, variant, subtle, pill, startIcon, endIcon, className, ...props }: BadgeProps) => import("react/jsx-runtime").JSX.Element;
export {};
