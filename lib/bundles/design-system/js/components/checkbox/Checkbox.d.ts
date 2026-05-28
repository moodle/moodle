import { InputHTMLAttributes } from 'react';
export interface CheckboxProps extends InputHTMLAttributes<HTMLInputElement> {
    /** Visible label text. When hideLabel is true this also serves as the aria-label fallback
     *  if no explicit aria-label prop is provided. */
    label?: string;
    /** When true, the visible label element is hidden. The input is still labelled accessibly
     *  via aria-label (prop) → label (prop) in that order of precedence. Suppresses
     *  invalidFeedback — feedback text requires a visible label to provide context. */
    hideLabel?: boolean;
    /** Marks the input as invalid: applies danger border/label colour and sets aria-invalid.
     *  Independent of invalidFeedback — invalid styling can be shown without a message. */
    invalid?: boolean;
    /** Renders the checkbox in a mixed state, typically for "select all" parent controls.
     *  This state is visual/semantic and should usually be controlled by parent logic. */
    indeterminate?: boolean;
    /** Optional supporting/helper text shown below the label in non-error state.
     *  Hidden when hideLabel is true. */
    supportingText?: string;
    /** Pre-translated error message rendered below the label. Requires invalid={true} and
     *  hideLabel={false} to be displayed. */
    invalidFeedback?: string;
}
export declare const Checkbox: import('react').ForwardRefExoticComponent<CheckboxProps & import('react').RefAttributes<HTMLInputElement>>;
