import {
    imageButtonNameMathType,
    imageButtonNameChemType
} from './common';
import {
    addMenubarItem,
} from 'editor_tiny/utils';

// Name of the default equation editor in Tiny
const TINY_EQUATION = 'tiny_equation';

const configureMenu = (menu) => {
    if (menu.insert.items.includes(TINY_EQUATION)) {
        // By Moodle's recommendation, we replace the default formula editor with MathType and ChemType
        menu.insert.items = menu.insert.items.replace(TINY_EQUATION, `${imageButtonNameMathType} ${imageButtonNameChemType}`);
    } else {
        // If the default equation editor wasn't enabled, we just add MathType and ChemType
        addMenubarItem(menu, 'insert', imageButtonNameMathType);
        addMenubarItem(menu, 'insert', imageButtonNameChemType);
    }

    return menu;
};

const configureToolbar = (toolbar) => {
    // The toolbar contains an array of named sections.

    // First, replace the default equation editor with MathType and ChemType
    toolbar = toolbar.map((section) => {
        const buttonIndex = section.items.indexOf(TINY_EQUATION);
        if (buttonIndex > -1) {
            section.items.splice(buttonIndex, 1, imageButtonNameMathType, imageButtonNameChemType);
        }
        return section;
    });

    // If the default editor had been removed from the toolbar, MT and CT will not have been included.
    // In such case, add MathType and ChemType directly, in section `content`
    const allButtons = toolbar.flatMap((section) => section.items);
    const hasMathType = allButtons.includes(imageButtonNameMathType);
    const hasChemType = allButtons.includes(imageButtonNameChemType);
    if (!hasMathType || !hasChemType) {
        toolbar = toolbar.map((section) => {
            if (section.name === 'content') {
                if (!hasMathType) {
                    section.items.unshift(imageButtonNameChemType);
                }
                if (!hasChemType) {
                    section.items.unshift(imageButtonNameMathType);
                }
            }
            return section;
        });
    }

    return toolbar;
};

export const configure = (instanceConfig) => {
    // Update the instance configuration to add the Media menu option to the menus and toolbars and upload_handler.
    return {
        toolbar: configureToolbar(instanceConfig.toolbar),
        menu: configureMenu(instanceConfig.menu),
    };
};
