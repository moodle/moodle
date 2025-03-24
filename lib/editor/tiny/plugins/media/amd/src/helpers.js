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
 * Tiny media plugin helpers for image and embed.
 *
 * @module      tiny_media/helpers
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Selectors from './selectors';
import Config from 'core/config';

/**
 * Renders and inserts the body template for inserting an media into the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 */
export const body = async(templateContext, root) => {
    return Templates.renderForPromise(templateContext.bodyTemplate, {...templateContext})
    .then(({html, js}) => {
        Templates.replaceNodeContents(root.querySelector(Selectors[templateContext.selector].elements.bodyTemplate), html, js);
        return;
    })
    .catch(error => {
        window.console.log(error);
    });
};

/**
 * Renders and inserts the footer template for inserting an media into the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 */
export const footer = async(templateContext, root) => {
    return Templates.renderForPromise(templateContext.footerTemplate, {...templateContext})
    .then(({html, js}) => {
        Templates.replaceNodeContents(root.querySelector(Selectors[templateContext.selector].elements.footerTemplate), html, js);
        return;
    })
    .catch(error => {
        window.console.log(error);
    });
};

/**
 * Set extra properties on an instance using incoming data.
 *
 * @param {object} instance
 * @param {object} data
 * @return {object} Modified instance
 */
export const setPropertiesFromData = async(instance, data) => {
    for (const property in data) {
        if (typeof data[property] !== 'function') {
            instance[property] = data[property];
        }
    }
    return instance;
};

/**
 * Check if given string is a valid URL.
 *
 * @param {String} urlString URL the link will point to.
 * @returns {boolean} True is valid, otherwise false.
 */
export const isValidUrl = urlString => {
    const urlPattern = new RegExp('^(https?:\\/\\/)?' + // Protocol.
                                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // Domain name.
                                '((\\d{1,3}\\.){3}\\d{1,3})|localhost)' + // OR ip (v4) address, localhost.
                                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'); // Port and path.
    return !!urlPattern.test(urlString);
};

/**
 * Hide the element(s).
 *
 * @param {string|string[]} elements - The CSS selector for the elements to toggle.
 * @param {object} root - The CSS selector for the elements to toggle.
 */
export const hideElements = (elements, root) => {
    if (elements instanceof Array) {
        elements.forEach((elementSelector) => {
            const element = root.querySelector(elementSelector);
            if (element) {
                element.classList.add('d-none');
            }
        });
    } else {
        const element = root.querySelector(elements);
        if (element) {
            element.classList.add('d-none');
        }
    }
};

/**
 * Show the element(s).
 *
 * @param {string|string[]} elements - The CSS selector for the elements to toggle.
 * @param {object} root - The CSS selector for the elements to toggle.
 */
export const showElements = (elements, root) => {
    if (elements instanceof Array) {
        elements.forEach((elementSelector) => {
            const element = root.querySelector(elementSelector);
            if (element) {
                element.classList.remove('d-none');
            }
        });
    } else {
        const element = root.querySelector(elements);
        if (element) {
            element.classList.remove('d-none');
        }
    }
};

/**
 * Displays the upload loader and disables UI elements while loading a file.
 *
 * @param {html} root Modal element
 * @param {string} selector String of type IMAGE/EMBED
 */
export const startMediaLoading = (root, selector) => {
    showElements(Selectors[selector].elements.loaderIcon, root);
    const elementsToHide = [
        Selectors[selector].elements.insertMedia,
        Selectors[selector].elements.urlWarning,
        Selectors[selector].elements.modalFooter,
    ];
    hideElements(elementsToHide, root);
};

/**
 * Hide the upload loader and enable UI elements when loaded.
 *
 * @param {html} root Modal element
 * @param {string} selector String of type IMAGE/EMBED
 */
export const stopMediaLoading = (root, selector) => {
    hideElements(Selectors[selector].elements.loaderIcon, root);
    const elementsToShow = [
        Selectors[selector].elements.insertMedia,
        Selectors[selector].elements.modalFooter,
    ];
    showElements(elementsToShow, root);
};

/**
 * Return true or false if the url is external.
 *
 * @param {string} url
 * @returns {boolean} True if the URL is external, otherwise false.
 */
export const isExternalUrl = (url) => {
    const regex = new RegExp(`${Config.wwwroot}`);

    // True if the URL is from external, otherwise false.
    return regex.test(url) === false;
};

/**
 * Set the string for the URL label element.
 *
 * @param {object} props - The label text to set.
 */
export const setFilenameLabel = (props) => {
    const urlLabelEle = props.root.querySelector(props.fileNameSelector);
    if (urlLabelEle) {
        urlLabelEle.innerHTML = props.label;
        urlLabelEle.setAttribute("title", props.label);
    }
};

/**
 * This function checks whether an image URL is local (within the same website's domain) or external (from an external source).
 * Depending on the result, it dynamically updates the visibility and content of HTML elements in a user interface.
 * If the image is local then we only show it's filename.
 * If the image is external then it will show full URL and it can be updated.
 *
 * @param {object} props
 */
export const sourceTypeChecked = (props) => {
    if (props.fetchedTitle) {
        props.label = props.fetchedTitle;
    } else {
        if (!isExternalUrl(props.source)) {
            // Split the URL by '/' to get an array of segments.
            const segments = props.source.split('/');
            // Get the last segment, which should be the filename.
            const filename = segments.pop().split('?')[0];
            // Show the file name.
            props.label = decodeURI(filename);
        } else {
            props.label = decodeURI(props.source);
        }
    }
    setFilenameLabel(props);
};

/**
 * Get filename from the name label.
 *
 * @param {string} fileLabel
 * @returns {string}
 */
export const getFileName = (fileLabel) => {
    if (fileLabel.includes('/')) {
        const split = fileLabel.split('/');
        let fileName = split[split.length - 1];
        fileName = fileName.split('.');
        if (fileName.length > 1) {
            return decodeURI(fileName.slice(0, (fileName.length - 1)).join('.'));
        } else {
            return decodeURI(fileName[0]);
        }
    } else {
        return decodeURI(fileLabel.split('.')[0]);
    }
};

/**
 * Return true or false if % is found.
 *
 * @param {string} value
 * @returns {boolean}
 */
export const isPercentageValue = (value) => {
    return value.match(/\d+%/);
};
