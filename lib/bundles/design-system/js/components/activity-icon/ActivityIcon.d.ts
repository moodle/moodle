import { HTMLAttributes } from 'react';
import { ActivityIconName } from './activityIconRegistry';
export type ActivityIconVariant = 'none' | 'default' | 'large';
export type ActivityIconSize = 'sm' | 'md' | 'lg' | 'xl';
export interface ActivityIconProps extends HTMLAttributes<HTMLSpanElement> {
    /**
     * Activity/resource icon key used to resolve the SVG asset from the registry.
     */
    icon: ActivityIconName;
    /**
     * Accessible text for the rendered image. Use an empty string for decorative icons.
     */
    alt?: string;
    /**
     * Visual container style around the icon.
     */
    variant?: ActivityIconVariant;
    /**
     * Icon size token.
     */
    size?: ActivityIconSize;
}
export declare const ActivityIcon: ({ icon, alt, variant, size, className, ...props }: ActivityIconProps) => import("react/jsx-runtime").JSX.Element;
