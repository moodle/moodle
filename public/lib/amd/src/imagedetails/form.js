// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Image details form.
 *
 * Renders the image-details controls (alternative text, decorative flag and display size) into a
 * container the caller owns, and reports the user's choices back.
 *
 * Use this when the surrounding dialogue belongs to the caller, for example an editor that presents
 * the controls as one step of a larger flow. Callers that just want the controls in a dialogue of
 * their own should use core/imagedetails/modal instead, which wraps this form in a save/cancel modal.
 *
 * @module      core/imagedetails/form
 * @copyright   2026 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import {getString} from 'core/str';

/** @var {number} The maximum length of the alternative text. */
export const MAX_ALT_LENGTH = 750;

/** @var {number} How long the "maximum length reached" message stays in the live region, in milliseconds. */
const MAXLENGTH_FEEDBACK_TIMEOUT = 4000;

const SELECTORS = {
    alt: '[data-region="alt"]',
    altCount: '[data-region="altcount"]',
    altMaxLengthFeedback: '[data-region="altmaxlengthfeedback"]',
    altWarning: '[data-region="altwarning"]',
    presentation: '[data-region="presentation"]',
    customSize: '[data-region="customsize"]',
    width: '[data-region="width"]',
    height: '[data-region="height"]',
    sizeOriginal: '[data-action="size-original"]',
    sizeCustom: '[data-action="size-custom"]',
};

/**
 * The image details form.
 */
export class ImageDetailsForm {

    /**
     * Create a form for one image.
     *
     * The natural dimensions drive the proportional width/height adjustment. Leave them at 0 for an
     * image whose pixel dimensions are not known (an SVG without them, for instance), and the size
     * controls are left out entirely.
     *
     * @param {HTMLElement} container The element to render the form into. Its contents are replaced.
     * @param {object} config
     * @param {number} [config.naturalWidth=0] The natural width of the image, in pixels.
     * @param {number} [config.naturalHeight=0] The natural height of the image, in pixels.
     * @param {string} [config.alt=''] The alternative text to start with.
     * @param {boolean} [config.presentation=false] Whether the image starts marked as decorative.
     * @param {number} [config.width=0] The display width to start with. Defaults to the natural width.
     * @param {number} [config.height=0] The display height to start with. Defaults to the natural height.
     * @param {boolean} [config.showPreview=false] Whether the form renders its own preview of the image.
     * @param {string} [config.previewUrl=''] The image URL, for the preview.
     * @param {string} [config.filename=''] The file name, shown under the preview.
     */
    constructor(container, config = {}) {
        this.container = container;
        this.naturalWidth = config.naturalWidth ?? 0;
        this.naturalHeight = config.naturalHeight ?? 0;
        this.hasDimensions = this.naturalWidth > 0 && this.naturalHeight > 0;
        this.initialAlt = config.alt ?? '';
        this.initialPresentation = config.presentation ?? false;
        this.showPreview = config.showPreview ?? false;
        this.previewUrl = config.previewUrl ?? '';
        this.filename = config.filename ?? '';

        // A display size differing from the natural size means the user is looking at a custom size.
        const width = config.width || this.naturalWidth;
        const height = config.height || this.naturalHeight;
        this.customActive = this.hasDimensions && (width !== this.naturalWidth || height !== this.naturalHeight);
        this.customSize = {width, height};

        this.sizeChangeCallback = null;
        this.maxLengthFeedbackToggle = false;
        this.maxLengthFeedbackTimeout = null;
    }

    /**
     * Render the form and start listening for changes.
     *
     * @returns {Promise<void>}
     */
    async render() {
        // The renderer supplies uniqid itself, which the template uses to keep its ids unique.
        const {html, js} = await Templates.renderForPromise('core/imagedetails/form', {
            alt: this.initialAlt,
            presentation: this.initialPresentation,
            width: this.customSize.width,
            height: this.customSize.height,
            hasdimensions: this.hasDimensions,
            custom: this.customActive,
            maxlengthalt: MAX_ALT_LENGTH,
            decorativehelpicon: {text: await getString('imagedecorative_help')},
            showpreview: this.showPreview,
            previewurl: this.previewUrl,
            filename: this.filename,
        });
        Templates.replaceNodeContents(this.container, html, js);

        this.updateAltCount();
        this.registerEventListeners();
    }

    /**
     * Register a callback fired whenever the chosen display size changes.
     *
     * Useful for a caller showing its own preview of the image at the chosen size.
     *
     * @param {function({width: number, height: number}): void} callback
     */
    onSizeChange(callback) {
        this.sizeChangeCallback = callback;
    }

    /**
     * Whether the user has marked the image as decorative.
     *
     * @returns {boolean}
     */
    isDecorative() {
        const presentation = this.element(SELECTORS.presentation);
        return !!(presentation && presentation.checked);
    }

    /**
     * The user's choices.
     *
     * A width and height of 0 means the user kept the original size, leaving it to the caller to
     * decide what the image is displayed at.
     *
     * @returns {{alt: string, presentation: boolean, width: number, height: number}}
     */
    getDetails() {
        const decorative = this.isDecorative();
        const alt = this.element(SELECTORS.alt);
        // Only report an explicit size when the user chose "Custom".
        const custom = this.customActive && this.hasDimensions;
        return {
            alt: decorative ? '' : (alt ? alt.value.trim() : ''),
            presentation: decorative,
            width: custom ? this.fieldValue(SELECTORS.width) : 0,
            height: custom ? this.fieldValue(SELECTORS.height) : 0,
        };
    }

    /**
     * The size the image is currently shown at, whichever size mode is chosen.
     *
     * Unlike getDetails(), this always reports real dimensions: for the original size it reports the
     * natural ones rather than 0. Use it when the caller has to commit to a size either way, for
     * example when writing width and height onto an img tag.
     *
     * @returns {{width: number, height: number}}
     */
    getDisplaySize() {
        return {
            width: this.fieldValue(SELECTORS.width) || this.naturalWidth,
            height: this.fieldValue(SELECTORS.height) || this.naturalHeight,
        };
    }

    /**
     * Check the form, showing the error state when it does not pass.
     *
     * A non-decorative image must have alternative text, otherwise it reaches the reader with no
     * description at all.
     *
     * @returns {boolean} whether the form passed.
     */
    validate() {
        const alt = this.element(SELECTORS.alt);
        if (!this.isDecorative() && (!alt || alt.value.trim() === '')) {
            this.element(SELECTORS.altWarning)?.classList.remove('d-none');
            this.setAriaInvalid(true);
            alt?.focus();
            return false;
        }
        return true;
    }

    /**
     * Find one element of the form.
     *
     * @param {string} selector
     * @returns {HTMLElement|null}
     * @private
     */
    element(selector) {
        return this.container.querySelector(selector);
    }

    /**
     * The integer value of one of the number fields.
     *
     * @param {string} selector
     * @returns {number}
     * @private
     */
    fieldValue(selector) {
        const field = this.element(selector);
        return field ? (parseInt(field.value, 10) || 0) : 0;
    }

    /**
     * Mark, or clear, the error state on the fields the alternative text rule covers.
     *
     * @param {boolean} invalid
     * @private
     */
    setAriaInvalid(invalid) {
        const value = invalid ? 'true' : 'false';
        this.element(SELECTORS.alt)?.setAttribute('aria-invalid', value);
        this.element(SELECTORS.presentation)?.setAttribute('aria-invalid', value);
    }

    /**
     * Update the character count, announcing the limit when it is reached.
     *
     * @private
     */
    async updateAltCount() {
        const alt = this.element(SELECTORS.alt);
        const count = this.element(SELECTORS.altCount);
        if (!alt || !count) {
            return;
        }
        count.textContent = alt.value.length;

        const feedback = this.element(SELECTORS.altMaxLengthFeedback);
        if (!feedback || alt.value.length < MAX_ALT_LENGTH) {
            return;
        }

        // The visible count does not convey that typing has stopped having an effect, so say so. The
        // alternating suffix makes the live region change even when the message itself is unchanged,
        // which is what gets it announced again.
        const message = await getString('maxlengthreached', 'core', MAX_ALT_LENGTH);
        feedback.textContent = message + (this.maxLengthFeedbackToggle ? '' : '.');
        this.maxLengthFeedbackToggle = !this.maxLengthFeedbackToggle;

        // Clear it afterwards so that someone navigating the region later does not meet a stale message.
        clearTimeout(this.maxLengthFeedbackTimeout);
        this.maxLengthFeedbackTimeout = setTimeout(() => {
            feedback.textContent = '';
        }, MAXLENGTH_FEEDBACK_TIMEOUT);
    }

    /**
     * Hide the "description required" warning once it no longer applies.
     *
     * @private
     */
    clearWarningIfResolved() {
        const alt = this.element(SELECTORS.alt);
        if (this.isDecorative() || (alt && alt.value.trim() !== '')) {
            this.element(SELECTORS.altWarning)?.classList.add('d-none');
            this.setAriaInvalid(false);
        }
    }

    /**
     * Enable or disable the alternative text in step with the decorative checkbox.
     *
     * @private
     */
    presentationChanged() {
        const alt = this.element(SELECTORS.alt);
        if (alt) {
            alt.disabled = this.isDecorative();
        }
        this.clearWarningIfResolved();
    }

    /**
     * Keep width and height in proportion, driven by whichever field changed.
     *
     * @param {boolean} fromHeight whether the height field is the one that changed.
     * @private
     */
    autoAdjust(fromHeight) {
        if (!this.hasDimensions) {
            return;
        }
        const widthField = this.element(SELECTORS.width);
        const heightField = this.element(SELECTORS.height);
        if (!widthField || !heightField) {
            return;
        }
        if (fromHeight) {
            const height = parseInt(heightField.value, 10) || 0;
            widthField.value = Math.round(height * this.naturalWidth / this.naturalHeight);
        } else {
            const width = parseInt(widthField.value, 10) || 0;
            heightField.value = Math.round(width * this.naturalHeight / this.naturalWidth);
        }
        this.customSize = {
            width: parseInt(widthField.value, 10) || 0,
            height: parseInt(heightField.value, 10) || 0,
        };
        this.notifySizeChange();
    }

    /**
     * Switch between the original and a custom display size.
     *
     * @param {boolean} custom
     * @private
     */
    setSizeMode(custom) {
        this.customActive = custom;
        const originalButton = this.element(SELECTORS.sizeOriginal);
        const customButton = this.element(SELECTORS.sizeCustom);
        const customBlock = this.element(SELECTORS.customSize);
        const widthField = this.element(SELECTORS.width);
        const heightField = this.element(SELECTORS.height);
        if (!originalButton || !customButton || !customBlock || !widthField || !heightField) {
            return;
        }

        customButton.classList.toggle('btn-primary', custom);
        customButton.classList.toggle('btn-outline-primary', !custom);
        customButton.setAttribute('aria-pressed', custom ? 'true' : 'false');
        originalButton.classList.toggle('btn-primary', !custom);
        originalButton.classList.toggle('btn-outline-primary', custom);
        originalButton.setAttribute('aria-pressed', custom ? 'false' : 'true');
        customBlock.classList.toggle('d-none', !custom);

        if (custom) {
            widthField.value = this.customSize.width;
            heightField.value = this.customSize.height;
        } else {
            widthField.value = this.naturalWidth;
            heightField.value = this.naturalHeight;
        }
        this.notifySizeChange();
    }

    /**
     * Tell the caller about the size currently chosen.
     *
     * @private
     */
    notifySizeChange() {
        if (!this.sizeChangeCallback) {
            return;
        }
        this.sizeChangeCallback(this.getDisplaySize());
    }

    /**
     * @private
     */
    registerEventListeners() {
        this.presentationChanged();

        this.container.addEventListener('input', (e) => {
            if (e.target.closest(SELECTORS.alt)) {
                this.updateAltCount();
                this.clearWarningIfResolved();
            } else if (e.target.closest(SELECTORS.width)) {
                this.autoAdjust(false);
            } else if (e.target.closest(SELECTORS.height)) {
                this.autoAdjust(true);
            }
        });

        this.container.addEventListener('change', (e) => {
            if (e.target.closest(SELECTORS.presentation)) {
                this.presentationChanged();
            }
        });

        this.element(SELECTORS.sizeOriginal)?.addEventListener('click', () => this.setSizeMode(false));
        this.element(SELECTORS.sizeCustom)?.addEventListener('click', () => this.setSizeMode(true));
    }
}
