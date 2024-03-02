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
 * TinyMCE Editor Manager.
 *
 * @module editor_tiny/editor
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import jQuery from 'jquery';
import Pending from 'core/pending';
import {getDefaultConfiguration} from './defaults';
import {getTinyMCE, baseUrl} from './loader';
import * as Options from './options';
import {addToolbarButton, addToolbarButtons, addToolbarSection,
    removeToolbarButton, removeSubmenuItem, updateEditorState} from './utils';

/**
 * Storage for the TinyMCE instances on the page.
 * @type {Map}
 */
const instanceMap = new Map();

/**
 * The default editor configuration.
 * @type {Object}
 */
let defaultOptions = {};

/**
 * Require the modules for the named set of TinyMCE plugins.
 *
 * @param {string[]} pluginList The list of plugins
 * @return {Promise[]} A matching set of Promises relating to the requested plugins
 */
const importPluginList = async(pluginList) => {
    // Fetch all of the plugins from the list of plugins.
    // If a plugin contains a '/' then it is assumed to be a Moodle AMD module to import.
    const pluginHandlers = await Promise.all(pluginList.map(pluginPath => {
        if (pluginPath.indexOf('/') === -1) {
            // A standard TinyMCE Plugin.
            return Promise.resolve(pluginPath);
        }

        return import(pluginPath);
    }));

    // Normalise the plugin data to a list of plugin names.
    // Two formats are supported:
    // - a string; and
    // - an array whose first element is the plugin name, and the second element is the plugin configuration.
    const pluginNames = pluginHandlers.map((pluginConfig) => {
        if (typeof pluginConfig === 'string') {
            return pluginConfig;
        }
        if (Array.isArray(pluginConfig)) {
            return pluginConfig[0];
        }
        return null;
    }).filter((value) => value);

    // Fetch the list of pluginConfig handlers.
    const pluginConfig = pluginHandlers.map((pluginConfig) => {
        if (Array.isArray(pluginConfig)) {
            return pluginConfig[1];
        }
        return null;
    }).filter((value) => value);

    return {
        pluginNames,
        pluginConfig,
    };
};

/**
 * Fetch the language data for the specified language.
 *
 * @param {string} language The language identifier
 * @returns {object}
 */
const fetchLanguage = (language) => fetch(
    `${M.cfg.wwwroot}/lib/editor/tiny/lang.php/${M.cfg.langrev}/${language}`
).then(response => response.json());

/**
 * Get a list of all Editors in a Map, keyed by the DOM Node that the Editor is associated with.
 *
 * @returns {Map<Node, Editor>}
 */
export const getAllInstances = () => new Map(instanceMap.entries());

/**
 * Get the TinyMCE instance for the specified Node ID.
 *
 * @param {string} elementId
 * @returns {TinyMCE|undefined}
 */
export const getInstanceForElementId = elementId => getInstanceForElement(document.getElementById(elementId));

/*
 * Get the TinyMCE instance for the specified HTMLElement.
 *
 * @param {HTMLElement} element
 * @returns {TinyMCE|undefined}
 */
export const getInstanceForElement = element => {
    const instance = instanceMap.get(element);
    if (instance && instance.removed) {
        instanceMap.remove(element);
        return undefined;
    }
    return instance;
};

/**
 * Set up TinyMCE for the selector at the specified HTML Node id.
 *
 * @param {object} config The configuration required to setup the editor
 * @param {string} config.elementId The HTML Node ID
 * @param {Object} config.options The editor plugin configuration
 * @return {Promise<TinyMCE>} The TinyMCE instance
 */
export const setupForElementId = ({elementId, options}) => {
    const target = document.getElementById(elementId);
    return setupForTarget(target, options);
};

/**
 * Initialise the page with standard TinyMCE requirements.
 *
 * Currently this includes the language taken from the HTML lang property.
 */
const initialisePage = async() => {
    const lang = document.querySelector('html').lang;

    const [tinyMCE, langData] = await Promise.all([getTinyMCE(), fetchLanguage(lang)]);
    tinyMCE.addI18n(lang, langData);
};
initialisePage();

/**
 * Get the list of plugins to load for the specified configuration.
 *
 * If the specified configuration does not include a plugin configuration, then return the default configuration.
 *
 * @param {object} options
 * @param {array} [options.plugins=null] The plugin list
 * @returns {object}
 */
const getPlugins = ({plugins = null} = {}) => {
    if (plugins) {
        return plugins;
    }

    if (defaultOptions.plugins) {
        return defaultOptions.plugins;
    }

    return {};
};

/**
 * Adjust the editor size base on the target element.
 *
 * @param {TinyMCE} editor TinyMCE editor
 * @param {Node} target Target element
 */
const adjustEditorSize = (editor, target) => {
    let expectedEditingAreaHeight = 0;
    if (target.clientHeight) {
        expectedEditingAreaHeight = target.clientHeight;
    } else {
        // If the target element is hidden, we cannot get the lineHeight of the target element.
        // We don't have a proper way to retrieve the general lineHeight of the theme, so we use 22 here, it's equivalent to 1.5em.
        expectedEditingAreaHeight = target.rows * (parseFloat(window.getComputedStyle(target).lineHeight) || 22);
    }
    const currentEditingAreaHeight = editor.getContainer().querySelector('.tox-sidebar-wrap').clientHeight;
    if (currentEditingAreaHeight < expectedEditingAreaHeight) {
        // Change the height based on the target element's height.
        editor.getContainer().querySelector('.tox-sidebar-wrap').style.height = `${expectedEditingAreaHeight}px`;
    }
};

/**
 * Get the standard configuration for the specified options.
 *
 * @param {Node} target
 * @param {tinyMCE} tinyMCE
 * @param {object} options
 * @param {Array} plugins
 * @returns {object}
 */
const getStandardConfig = (target, tinyMCE, options, plugins) => {
    const lang = document.querySelector('html').lang;

    const config = Object.assign({}, getDefaultConfiguration(), {
        // eslint-disable-next-line camelcase
        base_url: baseUrl,

        // Set the editor target.
        // https://www.tiny.cloud/docs/tinymce/6/editor-important-options/#target
        target,

        // https://www.tiny.cloud/docs/tinymce/6/customize-ui/#set-maximum-and-minimum-heights-and-widths
        // Set the minimum height to the smallest height that we can fit the Menu bar, Tool bar, Status bar and the text area.
        // eslint-disable-next-line camelcase
        min_height: 175,

        // Base the height on the size of the text area.
        // In some cases, E.g.: The target is an advanced element, it will be hidden. We cannot get the height at this time.
        // So set the height to auto, and adjust it later by adjustEditorSize().
        height: target.clientHeight || 'auto',

        // Set the language.
        // https://www.tiny.cloud/docs/tinymce/6/ui-localization/#language
        // eslint-disable-next-line camelcase
        language: lang,

        // Load the editor stylesheet into the editor iframe.
        // https://www.tiny.cloud/docs/tinymce/6/add-css-options/
        // eslint-disable-next-line camelcase
        content_css: [
            options.css,
        ],

        // Do not convert URLs to relative URLs.
        // https://www.tiny.cloud/docs/tinymce/6/url-handling/#convert_urls
        // eslint-disable-next-line camelcase
        convert_urls: false,

        // Enabled 'advanced' a11y options.
        // This includes allowing role="presentation" from the image uploader.
        // https://www.tiny.cloud/docs/tinymce/6/accessibility/
        // eslint-disable-next-line camelcase
        a11y_advanced_options: true,

        // Add specific rules to the valid elements.
        // eslint-disable-next-line camelcase
        extended_valid_elements: 'script[*],p[*],i[*]',

        // Disable XSS Sanitisation.
        // We do this in PHP.
        // https://www.tiny.cloud/docs/tinymce/6/security/#turning-dompurify-off
        // Note: This feature has been backported from TinyMCE 6.4.0.
        // eslint-disable-next-line camelcase
        xss_sanitization: false,

        // Disable quickbars entirely.
        // The UI is not ideal and we'll wait for it to improve in future before we enable it in Moodle.
        // eslint-disable-next-line camelcase
        quickbars_insert_toolbar: '',

        // Override the standard block formats property (removing h1 & h2).
        // https://www.tiny.cloud/docs/tinymce/6/user-formatting-options/#block_formats
        // eslint-disable-next-line camelcase
        block_formats: 'Paragraph=p; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',

        // The list of plugins to include in the instance.
        // https://www.tiny.cloud/docs/tinymce/6/editor-important-options/#plugins
        plugins: [
            ...plugins,
        ],

        // Skins
        skin: 'oxide',

        // Remove the "Upgrade" link for Tiny.
        // https://www.tiny.cloud/docs/tinymce/6/editor-premium-upgrade-promotion/
        promotion: false,

        // Allow the administrator to disable branding.
        // https://www.tiny.cloud/docs/tinymce/6/statusbar-configuration-options/#branding
        branding: options.branding,

        // Put th cells in a thead element.
        // https://www.tiny.cloud/docs/tinymce/6/table-options/#table_header_type
        // eslint-disable-next-line camelcase
        table_header_type: 'sectionCells',

        // Stored text in non-entity form.
        // https://www.tiny.cloud/docs/tinymce/6/content-filtering/#entity_encoding
        // eslint-disable-next-line camelcase
        entity_encoding: "raw",

        // Enable support for editors in scrollable containers.
        // https://www.tiny.cloud/docs/tinymce/6/ui-mode-configuration-options/#ui_mode
        // eslint-disable-next-line camelcase
        ui_mode: 'split',

        // Enable browser-supported spell checking.
        // https://www.tiny.cloud/docs/tinymce/latest/spelling/
        // eslint-disable-next-line camelcase
        browser_spellcheck: true,

        setup: (editor) => {
            Options.register(editor, options);

            editor.on('PreInit', function() {
                // Work around a bug in TinyMCE with Firefox.
                // When an editor is removed, and replaced with an identically attributed editor (same ID),
                // and the Firefox window is freshly opened (e.g. Behat, Private browsing), the wrong contentWindow
                // is assigned to the editor instance leading to an NS_ERROR_UNEXPECTED error in Firefox.
                // This is a workaround for that issue.
                this.contentWindow = this.iframeElement.contentWindow;
            });
            editor.on('init', function() {
                // Hide justify alignment sub-menu.
                removeSubmenuItem(editor, 'align', 'tiny:justify');
                // Adjust the editor size.
                adjustEditorSize(editor, target);
            });

            target.addEventListener('form:editorUpdated', function() {
                updateEditorState(editor, target);
            });

            target.dispatchEvent(new Event('form:editorUpdated'));
        },
    });

    config.toolbar = addToolbarSection(config.toolbar, 'content', 'formatting', true);
    config.toolbar = addToolbarButton(config.toolbar, 'content', 'link');

    // Add directionality plugins, always.
    config.toolbar = addToolbarSection(config.toolbar, 'directionality', 'alignment', true);
    config.toolbar = addToolbarButtons(config.toolbar, 'directionality', ['ltr', 'rtl']);

    // Remove the align justify button from the toolbar.
    config.toolbar = removeToolbarButton(config.toolbar, 'alignment', 'alignjustify');

    return config;
};

/**
 * Fetch the TinyMCE configuration for this editor instance.
 *
 * @param {HTMLElement} target
 * @param {TinyMCE} tinyMCE The TinyMCE API
 * @param {Object} options The editor plugin configuration
 * @param {object} pluginValues
 * @param {object} pluginValues.pluginConfig The list of plugin configuration
 * @param {object} pluginValues.pluginNames The list of plugins to load
 * @returns {object} The TinyMCE Configuration
 */
const getEditorConfiguration = (target, tinyMCE, options, pluginValues) => {
    const {
        pluginNames,
        pluginConfig,
    } = pluginValues;

    // Allow plugins to modify the configuration.
    // This seems a little strange, but we must double-process the config slightly.

    // First we fetch the standard configuration.
    const instanceConfig = getStandardConfig(target, tinyMCE, options, pluginNames);

    // Next we make any standard changes.
    // Here we remove the file menu, as it doesn't offer any useful functionality.
    // We only empty the items list so that a plugin may choose to add to it themselves later if they wish.
    if (instanceConfig.menu.file) {
        instanceConfig.menu.file.items = '';
    }

    // We disable the styles, backcolor, and forecolor plugins from the format menu.
    // These are not useful for Moodle and we don't want to encourage their use.
    if (instanceConfig.menu.format) {
        instanceConfig.menu.format.items = instanceConfig.menu.format.items
            // Remove forecolor and backcolor.
            .replace(/forecolor ?/, '')
            .replace(/backcolor ?/, '')

            // Remove fontfamily for now.
            .replace(/fontfamily ?/, '')

            // Remove fontsize for now.
            .replace(/fontsize ?/, '')

            // Remove styles - it just duplicates the format menu in a way which does not respect configuration
            .replace(/styles ?/, '')

            // Remove any duplicate separators.
            .replaceAll(/\| *\|/g, '|');
    }

    // eslint-disable-next-line camelcase
    instanceConfig.quickbars_selection_toolbar = instanceConfig.quickbars_selection_toolbar.replace('h2 h3', 'h3 h4 h5 h6');

    // Next we call the `configure` function for any plugin which defines it.
    // We pass the current instanceConfig in here, to allow them to make certain changes to the global configuration.
    // For example, to add themselves to any menu, toolbar, and so on.
    // Any plugin which wishes to have configuration options must register those options here.
    pluginConfig.filter((pluginConfig) => typeof pluginConfig.configure === 'function').forEach((pluginConfig) => {
        const pluginInstanceOverride = pluginConfig.configure(instanceConfig, options);
        Object.assign(instanceConfig, pluginInstanceOverride);
    });

    // Next we convert the plugin configuration into a format that TinyMCE understands.
    Object.assign(instanceConfig, Options.getInitialPluginConfiguration(options));

    return instanceConfig;
};

/**
 * Check if the target for TinyMCE is in a modal or not.
 *
 * @param {HTMLElement} target Target to check
 * @returns {boolean} True if the target is in a modal form.
 */
const isModalMode = (target) => {
    return !!target.closest('[data-region="modal"]');
};

/**
 * Set up TinyMCE for the HTML Element.
 *
 * @param {HTMLElement} target
 * @param {Object} [options={}] The editor plugin configuration
 * @return {Promise<TinyMCE>} The TinyMCE instance
 */
export const setupForTarget = async(target, options = {}) => {
    const instance = getInstanceForElement(target);
    if (instance) {
        return Promise.resolve(instance);
    }

    // Register a new pending promise to ensure that Behat waits for the editor setup to complete before continuing.
    const pendingPromise = new Pending('editor_tiny/editor:setupForTarget');

    // Get the list of plugins.
    const plugins = getPlugins(options);

    // Fetch the tinyMCE API, and instantiate the plugins.
    const [tinyMCE, pluginValues] = await Promise.all([
        getTinyMCE(),
        importPluginList(Object.keys(plugins)),
    ]);

    // TinyMCE uses the element ID as a map key internally, even if the target has changed.
    // In the case where we have an editor in a modal form which has been detached from the DOM, but the editor not removed,
    // we need to manually destroy the editor.
    // We could theoretically do this with a Mutation Observer, but in some cases the Node may be moved,
    // or added back elsewhere in the DOM.

    // First remove any detached editors.
    tinyMCE.get().filter((editor) => !editor.getElement().isConnected).forEach((editor) => {
        editor.remove();
    });

    // Now check for any existing editor which shares the same ID.
    const existingEditor = tinyMCE.EditorManager.get(target.id);
    if (existingEditor) {
        if (existingEditor.getElement() === target) {
            pendingPromise.resolve();
            return Promise.resolve(existingEditor);
        } else {
            pendingPromise.resolve();
            throw new Error('TinyMCE instance already exists for different target with same ID');
        }
    }

    // Get the editor configuration for this editor.
    const instanceConfig = getEditorConfiguration(target, tinyMCE, options, pluginValues);

    // Initialise the editor instance for the given configuration.
    // At this point any plugin which has configuration options registered will have them applied for this instance.
    const [editor] = await tinyMCE.init(instanceConfig);

    // Update the textarea when the editor to set the field type for Behat.
    target.dataset.fieldtype = 'editor';

    // Store the editor instance in the instanceMap and register a listener on removal to remove it from the map.
    instanceMap.set(target, editor);
    editor.on('remove', ({target}) => {
        // Handle removal of the editor from the map on destruction.
        instanceMap.delete(target.targetElm);
        target.targetElm.dataset.fieldtype = null;
    });

    // If the editor is part of a form, also listen to the jQuery submit event.
    // The jQuery submit event will not trigger the native submit event, and therefore the content will not be saved.
    // We cannot rely on listening to the bubbled submit event on the document because other events on child nodes may
    // consume the data before it is saved.
    if (target.form) {
        jQuery(target.form).on('submit', () => {
            editor.save();
        });
    }

    // Save the editor content to the textarea when the editor is blurred.
    editor.on('blur', () => {
        editor.save();
    });

    // If the editor is in a modal, we need to hide the modal when window editor's window is opened.
    editor.on('OpenWindow', () => {
        const modals = document.querySelectorAll('[data-region="modal"]');
        if (modals) {
            modals.forEach((modal) => {
                if (!modal.classList.contains('hide')) {
                    modal.classList.add('hide');
                }
            });
        }
    });

    // If the editor's window is closed, we need to show the hidden modal back.
    editor.on('CloseWindow', () => {
        if (isModalMode(target)) {
            const modals = document.querySelectorAll('[data-region="modal"]');
            if (modals) {
                modals.forEach((modal) => {
                    if (modal.classList.contains('hide')) {
                        modal.classList.remove('hide');
                    }
                });
            }
        }
    });

    pendingPromise.resolve();
    return editor;
};

/**
 * Set the default editor configuration.
 *
 * This configuration is used when an editor is initialised without any configuration.
 *
 * @param {object} [options={}]
 */
export const configureDefaultEditor = (options = {}) => {
    defaultOptions = options;
};
