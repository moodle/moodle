import { HTMLAttributes } from 'react';
type ProgressBarStatus = 'in-progress' | 'loading' | 'error' | 'warning';
type ProgressBarLabelVariant = 'title-and-count' | 'title' | 'inline' | 'none';
export interface ProgressBarProps extends HTMLAttributes<HTMLDivElement> {
    /** Current progress value in the range defined by `min` and `max`. */
    value?: number;
    /** Lower bound for the progress range. Defaults to 0. */
    min?: number;
    /** Upper bound for the progress range. Defaults to 100. */
    max?: number;
    /** Visual state of the progress indicator — controls fill and track colours. */
    status?: ProgressBarStatus;
    /**
     * Controls label layout:
     * - `title-and-count`: title + count row above the bar (default)
     * - `title`: title only above the bar
     * - `inline`: bar with count beside it in a row
     * - `none`: bar only, no visible label
     */
    labelVariant?: ProgressBarLabelVariant;
    /** Pre-translated title text rendered above the bar (title-and-count / title variants).
     *  Also used as the accessible name for the bar when no visible label is present. */
    title?: string;
    /**
     * Pre-translated count or percentage text, e.g. "3 of 10" or "50%".
     * The component intentionally does not calculate or format this from
     * `value`, `min`, and `max`; word order, plural rules, and number formatting
     * belong to the consuming application's i18n layer.
     */
    count?: string;
    /** Controls striped animation when status is loading. */
    animated?: boolean;
}
export declare const ProgressBar: ({ value, min, max, status, labelVariant, title, count, animated, className, ...props }: ProgressBarProps) => import("react").JSX.Element;
export {};
