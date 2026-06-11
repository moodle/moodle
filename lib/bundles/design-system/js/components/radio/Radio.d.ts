import { InputHTMLAttributes } from 'react';
export interface RadioProps extends InputHTMLAttributes<HTMLInputElement> {
    /** Visible label text. When hideLabel is true this also serves as the aria-label fallback
     *  if no explicit aria-label prop is provided. */
    label?: string;
    /** When true, the visible label element is hidden. The input is still labelled accessibly
     *  via aria-label (prop) → label (prop) in that order of precedence. Suppresses
     *  invalidFeedback — feedback text requires a visible label to provide context.
     *
     *  Use cases: hideLabel is appropriate when the visual label would be redundant or visually cluttered,
     *  such as in dense tables where the column header acts as the label, or in icon-only UIs. Always ensure
     *  an accessible name is provided via aria-label or label prop. */
    hideLabel?: boolean;
    /** Marks the input as invalid: applies danger border/label colour and sets aria-invalid.
     *  Independent of invalidFeedback — invalid styling can be shown without a message. */
    invalid?: boolean;
    /** Pre-translated error message rendered below the label. Requires invalid={true} and
     *  hideLabel={false} to be displayed. Only invalid feedback is supported; is-valid
     *  and neutral feedback states are intentionally not implemented. */
    invalidFeedback?: string;
}
export declare const Radio: import('react').ForwardRefExoticComponent<RadioProps & import('react').RefAttributes<HTMLInputElement>>;
