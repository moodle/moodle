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

import Pending from 'core/pending';
import {getDefaultConfiguration} from './defaults';
import {getTinyMCE, baseUrl} from './loader';
import * as Options from './options';

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

    return Object.assign({}, getDefaultConfiguration(), {
        // eslint-disable-next-line camelcase
        base_url: baseUrl,

        // Set the editor target.
        // https://www.tiny.cloud/docs/tinymce/6/editor-important-options/#target
        target,

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

        // Disable quickbars entirely.
        // The UI is not ideal and we'll wait for it to improve in future before we enable it in Moodle.
        // eslint-disable-next-line camelcase
        quickbars_insert_toolbar: '',

        // The list of plugins to include in the instance.
        // https://www.tiny.cloud/docs/tinymce/6/editor-important-options/#plugins
        plugins: [
            ...plugins,
        ],

        // TODO Add mobile configuration.
        // Mobile configuration.
        // https://www.tiny.cloud/docs/tinymce/6/tinymce-for-mobile/
        // This will include mobile-specific toolbar, and menu options.

        // Skins
        skin: 'oxide',

        // Remove the "Upgrade" link for Tiny.
        // https://www.tiny.cloud/docs/tinymce/6/editor-premium-upgrade-promotion/
        promotion: false,

        // Allow the administrator to disable branding.
        // https://www.tiny.cloud/docs/tinymce/6/statusbar-configuration-options/#branding
        branding: options.branding,

        setup: (editor) => {
            Options.register(editor, options);
        },
    });
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
    const {pluginNames, pluginConfig} = pluginValues;

    // TinyMCE uses the element ID as a map key internally, even if the target has changed.
    // In the case where we have an editor in a modal form which has been detached from the DOM, but the editor not removed,
    // we need to manually destroy the editor.
    // We could theoretically do this with a Mutation Observer, but in some cases the Node may be moved,
    // or added back elsewhere in the DOM.
    const existingEditor = tinyMCE.EditorManager.get(target.id);
    if (existingEditor) {
        if (existingEditor.targetElm.closest('body')) {
            if (existingEditor.targetElm === target) {
                pendingPromise.resolve();
                return Promise.resolve(existingEditor);
            } else {
                pendingPromise.resolve();
                throw new Error('TinyMCE instance already exists for different target with same ID');
            }
        } else {
            existingEditor.destroy();
        }
    }

    // Allow plugins to modify the configuration.
    const instanceConfig = getStandardConfig(target, tinyMCE, options, pluginNames);
    pluginConfig.forEach((pluginConfig) => {
        if (typeof pluginConfig.configure === 'function') {
            Object.assign(instanceConfig, pluginConfig.configure(instanceConfig, options));
        }
    });
    Object.assign(instanceConfig, Options.getInitialPluginConfiguration(options));

    // Initialise the editor instance for the given configuration.
    const [editor] = await tinyMCE.init(instanceConfig);

    // Store the editor instance in the instanceMap and register its removal to remove it.
    instanceMap.set(target, editor);
    editor.on('remove', ({target}) => {
        // Handle removal of the editor from the map on destruction.
        instanceMap.delete(target.targetElm);
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
