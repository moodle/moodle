import { ButtonHTMLAttributes } from 'react';
export interface FavouriteButtonProps extends Omit<ButtonHTMLAttributes<HTMLButtonElement>, 'type'> {
    /**
     * Whether the item is currently selected as a favourite.
     * Controls the filled/outlined icon state and `aria-pressed`.
     */
    selected?: boolean;
    /**
     * Accessible name announced by screen readers.
     * Must be a translated string provided by the caller.
     * Typically "Add to favourites" or "Remove from favourites".
     */
    'aria-label': string;
}
export declare const FavouriteButton: import('react').ForwardRefExoticComponent<FavouriteButtonProps & import('react').RefAttributes<HTMLButtonElement>>;
