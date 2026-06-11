import { ButtonHTMLAttributes, ReactElement } from 'react';
type ButtonVariant = 'primary' | 'secondary' | 'danger' | 'ghost' | 'outline-primary' | 'outline-secondary' | 'outline-danger';
type ButtonSize = 'sm' | 'md' | 'lg';
type IconElement = ReactElement<'i' | 'svg'>;
export interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    label?: string;
    variant?: ButtonVariant;
    size?: ButtonSize;
    startIcon?: IconElement;
    endIcon?: IconElement;
}
export declare const Button: import('react').ForwardRefExoticComponent<ButtonProps & import('react').RefAttributes<HTMLButtonElement>>;
export {};
