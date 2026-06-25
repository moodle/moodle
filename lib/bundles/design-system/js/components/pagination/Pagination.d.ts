import { ComponentPropsWithoutRef } from 'react';
import { PageLabelFormatter } from './pagination.helpers';
export interface PaginationProps extends ComponentPropsWithoutRef<'nav'> {
    /** Total number of pages */
    totalPages: number;
    /** Current page number (1-indexed) */
    currentPage: number;
    /** Callback fired when the page changes */
    onPageChange: (page: number) => void;
    /** Accessible name for the pagination landmark. */
    ariaLabel?: string;
    /** Accessible label used for the previous-page button. */
    previousPageLabel?: string;
    /** Accessible label used for the next-page button. */
    nextPageLabel?: string;
    /** Returns the accessible label for each numbered page button. */
    pageLabelFormatter?: PageLabelFormatter;
    /**
     * Controls which variant of pagination to render.
     * Accepts a broad string so JS consumers can be validated at runtime.
     * - `'full'` (default): Shows page numbers between previous and next controls.
     *   The visible page count reduces automatically as the viewport width narrows
     *   (9 → 7 → 5 items), and collapses to grouped appearance when the viewport
     *   is too narrow to fit any page numbers. First and last pages are always shown
     *   when needed.
     * - `'grouped'`: Shows only previous and next controls without page numbers.
     */
    variant?: string;
    /** Disables all interactive elements, preventing focus, hover, and page-change events. */
    disabled?: boolean;
}
export declare const Pagination: ({ totalPages, currentPage, onPageChange, ariaLabel, previousPageLabel, nextPageLabel, pageLabelFormatter, variant, disabled, className, ...props }: PaginationProps) => import("react").JSX.Element | null;
