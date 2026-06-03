import { ButtonHTMLAttributes } from 'react';
export interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    label: string;
    variant?: string;
    size?: 'sm' | 'lg';
}
export declare const Button: ({ label, variant, size, className, type, ...props }: ButtonProps) => import("react/jsx-runtime").JSX.Element;
