import { ButtonHTMLAttributes } from 'react';
export interface CloseButtonProps extends Omit<ButtonHTMLAttributes<HTMLButtonElement>, 'type'> {
    /**
     * Accessible name announced by screen readers for the close button control.
     * Must be a translated string provided by the caller.
     */
    'aria-label': string;
    /**
     * Visual size variant for the close icon button.
     * Invalid values fall back to the default `md` size at runtime.
     */
    size?: string;
}
export declare const CloseButton: ({ "aria-label": ariaLabel, size, className, ...props }: CloseButtonProps) => import("react/jsx-runtime").JSX.Element;
