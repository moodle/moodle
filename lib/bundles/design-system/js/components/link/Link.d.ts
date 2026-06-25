import { AnchorHTMLAttributes, ReactElement } from 'react';
type IconElement = ReactElement<'i' | 'svg'>;
export interface LinkProps extends AnchorHTMLAttributes<HTMLAnchorElement> {
    label: string;
    variant?: string;
    disabled?: boolean;
    startIcon?: IconElement;
    endIcon?: IconElement;
}
export declare const Link: import('react').ForwardRefExoticComponent<LinkProps & import('react').RefAttributes<HTMLAnchorElement>>;
export {};
