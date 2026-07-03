import { HTMLAttributes } from 'react';
export type AvatarSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'xxl';
export interface AvatarProps extends HTMLAttributes<HTMLSpanElement> {
    /**
     * Visual size of the avatar.
     * xs=16px, sm=24px, md=32px, lg=48px, xl=64px, xxl=96px.
     * Defaults to 'md'.
     */
    size?: AvatarSize;
    /**
     * URL for the avatar photo. When provided, the image is displayed on top of
     * the initials layer. If the image fails to load, the component automatically
     * falls back to displaying the initials.
     */
    imageSrc?: string;
    /**
     * Accessible name for the avatar. When an image is visible it is forwarded
     * to the inner `<img alt>` attribute; when the component falls back to
     * initials or the SVG silhouette it is applied as `aria-label` on the root
     * element so the accessible name is preserved regardless of display state.
     * Use the user's full name (e.g. `"Jane Doe"`).
     * Omit or leave empty when the avatar is purely decorative.
     */
    alt?: string;
    /**
     * 1–2 character string used as the fallback when no `imageSrc` is provided
     * or when the image fails to load. The consuming app is responsible for
     * truncation and locale (e.g. derive from the user's display name).
     * When omitted or empty the SVG silhouette placeholder is shown instead.
     */
    initials?: string;
}
export declare const Avatar: import('react').ForwardRefExoticComponent<AvatarProps & import('react').RefAttributes<HTMLSpanElement>>;
