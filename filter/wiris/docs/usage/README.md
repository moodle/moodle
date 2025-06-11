# Using MathType filter

MathType filter renders any MathML or LaTeX expression into an image. To use it follow this guide:

1. **Enable MathType Filter**: MathType filter must be enabled by the site administrator. This can be done by going to Site administration > Plugins > Filters > Manage filters. Make sure MathType is listed before MathJax, and turned on.

2. **Check MathType/ChemType button existence**: When MathType filter is installed with the other dependencies listed in the [environment requirements](../environment/README.md#requirements) enabled, the MathType button will appear in Moodle's HTML editor whenever you're editing text in activities.

3. **Create an Equation**: 
    * Click the MathType/ChemType button.
    * The MathType editor will pop up.
    * Use the editor's modal to create your equation.
    * Click the "Insert" button, and the equation will appear in the Moodle editor.

4. **Save and display**: Save and display the changes in the Moodle activity. The equation will display as a rendered image.

> Note: LaTeX expressions can also be edited with MathType and rendered afterwards.


## Production

Follow this [guideline offered by Moodle](https://docs.moodle.org/405/en/Installing_plugins#Installing_a_plugin) to learn how to install any plugin to a production environment.

The Wiris [MathType filter](https://moodle.org/plugins/filter_wiris) can be found on the Moodle plugins directory, or clicking on the previous link.
