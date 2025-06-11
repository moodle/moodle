# Using MathType Moodle plugin for TinyMCE

This plugin allows insert any mathematic expression into TinyMCE editor field. To use it follow this guide:

## Preparation
To render the generated formulas, **Enable MathType Filter**: MathType filter must be enabled by the site administrator. This can be done by going to Site administration > Plugins > Filters > Manage filters. Make sure MathType is listed before MathJax, and turned on.

## Create equation
1. **Enable MathType Editor**: It must be enabled by the site administrator. This can be done by going to Site administration > Plugins > Filters > MathType by WIRIS. Make sure Math editor or/and Chemistry editor are checked.

2. **Check MathType/ChemType button existence**: The MathType button will appear in Moodle's TinyMCE editor whenever you're editing text in activities.

3. **Create an Equation**: 
    * Click the MathType/ChemType button.
    * The MathType editor will pop up.
    * Use the editor's modal to create your equation.
    * Click the "Insert" button, and the equation will appear in the TinyMCE editor.

## Production

Follow this [guideline offered by Moodle](https://docs.moodle.org/405/en/Installing_plugins#Installing_a_plugin) to learn how to install any plugin to a production environment.

The Wiris [MathType Moodle plugin for TinyMCE](https://moodle.org/plugins/tiny_wiris) can be found on the Moodle plugins directory, or clicking on the previous link.
