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
 * Utility functions.
 *
 * @module editor_tiny/editor
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {
    getTinyMCE,
} from './loader';
import Pending from 'core/pending';

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
    const pluginHandlers = await Promise.all(pluginList.map(pluginPath => {
        if (pluginPath.indexOf('/') === -1) {
            // A standard TinyMCE Plugin.
            return Promise.resolve(pluginPath);
        }

        return import(pluginPath);
    }));

    const pluginNames = pluginHandlers.map((pluginConfig) => {
        if (typeof pluginConfig === 'string') {
            return pluginConfig;
        }
        if (Array.isArray(pluginConfig)) {
            return pluginConfig[0];
        }
        return null;
    }).filter((value) => value);

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

const fetchLanguage = (language) => fetch(
    `${M.cfg.wwwroot}/lib/editor/tiny/lang.php/${M.cfg.langrev}/${language}`
).then(response => response.json());

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

const initialisePage = async() => {
    const lang = document.querySelector('html').lang;

    const [tinyMCE, langData] = await Promise.all([getTinyMCE(), fetchLanguage(lang)]);
    tinyMCE.addI18n(lang, langData);
};
initialisePage();

const getPlugins = (options) => {
    if (options.plugins) {
        return options.plugins;
    }

    if (defaultOptions.plugins) {
        return defaultOptions.plugins;
    }

    return {};
};

const getStandardConfig = (target, tinyMCE, options, plugins) => {
    const lang = document.querySelector('html').lang;
    return {
        // Set the editor target.
        // https://www.tiny.cloud/docs/tinymce/6/editor-important-options/#target
        target,

        // Set the language.
        // https://www.tiny.cloud/docs/tinymce/6/ui-localization/#language
        language: lang,

        // Load the editor stylesheet into the editor iframe.
        // https://www.tiny.cloud/docs/tinymce/6/add-css-options/
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

        // Toolbar configuration.
        // https://www.tiny.cloud/docs/tinymce/6/toolbar-configuration-options/
        // TODO: Move this configuration to a passed-in option.
        // eslint-disable-next-line camelcase
        toolbar_mode: 'sliding',
        toolbar: [
            {
                name: 'history',
                items: [
                    'undo',
                    'redo'
                ]
            },
            {
                name: 'styles',
                items: ['styles']
            },
            {
                name: 'formatting',
                items: [
                    'bold',
                    'italic'
                ]
            },
            {
                name: 'alignment',
                items: [
                    'alignleft',
                    'aligncenter',
                    'alignright',
                    'alignjustify'
                ]
            },
            {
                name: 'indentation',
                items: [
                    'outdent',
                    'indent'
                ]
            },
            {
                name: 'comments',
                items: ['addcomment']
            },
        ],

        // Menu configuration.
        // https://www.tiny.cloud/docs/tinymce/6/menus-configuration-options/
        // TODO: Move this configuration to a passed-in option.
        menu: {
        },

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
    };
};

/**
 * Set up TinyMCE for the HTML Element.
 *
 * @param {HTMLElement} target
 * @param {Object} options The editor plugin configuration
 * @return {Promise<TinyMCE>} The TinyMCE instance
 */
export const setupForTarget = async(target, options = {}) => {
    const instance = getInstanceForElement(target);
    if (instance) {
        return Promise.resolve(instance);
    }

    const pendingPromise = new Pending('editor_tiny/editor:setupForTarget');

    const plugins = getPlugins(options);
    const [tinyMCE, pluginValues] = await Promise.all([
        getTinyMCE(),
        importPluginList(Object.keys(plugins)),
    ]);
    const {pluginNames, pluginConfig} = pluginValues;

    const instanceConfig = getStandardConfig(target, tinyMCE, options, pluginNames);
    pluginConfig.forEach((pluginConfig) => {
        if (typeof pluginConfig.configure === 'function') {
            Object.assign(instanceConfig, pluginConfig.configure(instanceConfig));
        }
    });
    const [editor] = await tinyMCE.init(instanceConfig);

    // Store the editor instance in the instanceMap and register its removal to remove it.
    instanceMap.set(target, editor);
    editor.on('remove', ({target}) => {
        // Handle removal of the editor from the map on destruction.
        instanceMap.delete(target.targetElm);
    });

    // Store the Moodle-specific options in the TinyMCE instance.
    // TODO: See if there is a more appropriate location for this config.
    // TinyMCE does support custom configuration options in its EditorOptions but these must be registered and spec'd.
    editor.moodleOptions = options;

    pendingPromise.resolve();
    return editor;
};

export const configureDefaultEditor = (options = {}) => {
    defaultOptions = options;
};
