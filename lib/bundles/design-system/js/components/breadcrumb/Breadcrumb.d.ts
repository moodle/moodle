import { ComponentPropsWithoutRef } from 'react';
export interface BreadcrumbItem {
    /** Visible label text. Must be a caller-supplied translated string. */
    label: string;
    /** Destination URL for this breadcrumb item. Omit or leave undefined for the current page. */
    href?: string;
}
export interface BreadcrumbProps extends ComponentPropsWithoutRef<'nav'> {
    /**
     * Ordered list of breadcrumb items from root to current page.
     * The last item is always the current page and is rendered as plain text.
     * Must have at least 2 entries.
     */
    items: BreadcrumbItem[];
    /**
     * Accessible label for the `<nav>` landmark element.
     * Must be a caller-supplied translated string — the default is English only.
     */
    ariaLabel?: string;
    /**
     * Visible-to-assistive-tech label for the overflow "…" button.
     * Only relevant when the component renders with more than 4 items.
     * Must be a caller-supplied translated string — the default is English only.
     */
    overflowAriaLabel?: string;
}
export declare const Breadcrumb: ({ items, ariaLabel, overflowAriaLabel, className, ...props }: BreadcrumbProps) => import("react").JSX.Element | null;
