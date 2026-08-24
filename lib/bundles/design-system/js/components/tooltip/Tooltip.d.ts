import { HTMLAttributes, ReactElement } from 'react';
type TooltipPlacement = 'top' | 'bottom' | 'left' | 'right';
type TooltipVariant = 'dark' | 'light';
export interface TooltipProps extends HTMLAttributes<HTMLDivElement> {
    /** Tooltip text displayed in the popup. Must be a caller-supplied translated string. */
    label: string;
    /** Preferred physical side of the trigger the tooltip appears on. Defaults to `top`. Auto-flips to avoid viewport edges. In RTL, `left` and `right` still mean viewport-left and viewport-right. */
    placement?: TooltipPlacement;
    /** Colour mode. `dark` uses a dark background with light text; `light` uses a light background with dark text. Defaults to `dark`. */
    variant?: TooltipVariant;
    /** The trigger element. Must be a single React element that forwards refs. */
    children: ReactElement;
}
export declare const Tooltip: ({ label, placement, variant, children, className, ...props }: TooltipProps) => import("react").JSX.Element;
export {};
