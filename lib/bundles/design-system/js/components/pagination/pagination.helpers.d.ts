export type PaginationVariant = 'full' | 'grouped';
export type PageLabelFormatter = (page: number) => string;
interface ResolvedPaginationInputs {
    resolvedVariant: PaginationVariant;
    resolvedPageLabelFormatter: PageLabelFormatter;
    sanitizedTotalPages: number;
    sanitizedCurrentPage: number;
}
export declare const MAX_VISIBLE_ELEMENTS = 9;
/**
 * Calculate which page numbers to display given the current page, total pages,
 * and the maximum number of visible slots (boundaries + ellipses + center pages).
 *
 * Slot accounting for any maxVisible M:
 *   Near start / near end (1 ellipsis): 1 boundary + M-3 center + 1 ellipsis + 1 boundary
 *   Middle (2 ellipses):                1 boundary + 1 ellipsis + M-4 center + 1 ellipsis + 1 boundary
 */
export declare function calculateVisiblePageNumbers(currentPage: number, totalPages: number, maxVisible: number): {
    showBoundaryPages: boolean;
    showLeftEllipsis: boolean;
    showRightEllipsis: boolean;
    pageNumbers: number[];
};
export declare function useViewportMaxVisible(): number | null;
export declare function resolvePaginationInputs(variant: string, pageLabelFormatter: PageLabelFormatter | undefined, totalPages: number, currentPage: number): ResolvedPaginationInputs;
export {};
