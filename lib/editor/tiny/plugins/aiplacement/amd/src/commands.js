// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Commands helper for the Moodle tiny_aiplacement plugin.
 *
 * @module      tiny_aiplacement/commands
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage, addDynamicToolbarMenu} from 'editor_tiny/utils';
import {get_string as getString} from 'core/str';
import {
    component,
    placement,
    contextMenuName,
    generateImageName,
    generateTextName,
    contextMenuIcon,
    generateImageIcon,
    generateTextIcon
} from './common';
import GenerateImage from './generateimage';
import GenerateText from './generatetext';
import {isTextAllowed, isImageAllowed} from './options';

/**
 * Get the setup function for the buttons.
 *
 * This is performed in an async function which ultimately returns the registration function as the
 * Tiny.AddOnManager.Add() function does not support async functions.
 *
 * @returns {function} The registration function to call within the Plugin.add function.
 */
export const getSetup = async() => {
    const [
        contextMenuIconText,
        generateImageIconText,
        generateTextIconText,
        contextMenuIconImage,
        generateImageIconImage,
        generateTextIconImage,
    ] = await Promise.all([
        getString('generatecontent', placement),
        getString('generateimage', placement),
        getString('generatetext', placement),
        getButtonImage(contextMenuIcon, component),
        getButtonImage(generateImageIcon, component),
        getButtonImage(generateTextIcon, component),
    ]);

    const getToolbarParts = (editor) => {
        // Add the context menu button.
        const menuItems = [];

        if (isTextAllowed(editor)) {
            const textGenerator = new GenerateText(editor);

            editor.ui.registry.addMenuItem(generateTextName, {
                icon: generateTextIcon,
                text: generateTextIconText,
                onAction: () => {
                    textGenerator.displayContentModal(editor);
                },
            });

            menuItems.push(generateTextName);

            if (!isImageAllowed(editor)) {
                // Only text generation is allowed.
                return {
                    menuItems,
                    singleButton: generateTextIcon,
                    singleButtonTitle: generateTextIconText,
                    generator: textGenerator,
                };
            }
        }

        if (isImageAllowed(editor)) {
            const imageGenerator = new GenerateImage(editor);

            editor.ui.registry.addMenuItem(generateImageName, {
                icon: generateImageIcon,
                text: generateImageIconText,
                onAction: () => {
                    imageGenerator.displayContentModal(editor);
                },
            });

            menuItems.push(generateImageName);

            if (!isTextAllowed(editor)) {
                // Only image generation is allowed.
                return {
                    menuItems,
                    singleButton: generateImageIcon,
                    singleButtonTitle: generateImageIconText,
                    generator: imageGenerator,
                };
            }
        }

        return {
            menuItems,
            singleButton: null,
            singleButtonTitle: null,
            generator: null,
        };
    };


    return (editor) => {
        if (!isTextAllowed(editor) && !isImageAllowed(editor)) {
            return;
        }

        // Register the icon SVG files as an icon suitable for use in TinyMCE toolbars and buttons.
        editor.ui.registry.addIcon(contextMenuIcon, contextMenuIconImage.html);
        editor.ui.registry.addIcon(generateImageIcon, generateImageIconImage.html);
        editor.ui.registry.addIcon(generateTextIcon, generateTextIconImage.html);

        const {menuItems, singleButton, singleButtonTitle, generator} = getToolbarParts(editor);

        addDynamicToolbarMenu(
            editor,
            menuItems,
            contextMenuName,
            contextMenuIcon,
            contextMenuIconText,
            singleButton,
            singleButtonTitle,
            () => {
                generator.displayContentModal(editor);
            },
        );
    };
};
