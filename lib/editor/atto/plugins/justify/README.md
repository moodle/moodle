# moodle_atto_justify

Moodle Module for adding text functions in the Atto editor.


## Instalação

 ```
 git clone https://github.com/CoticEaDIFRN/moodle_atto_justify.git justify

 - Put the plugin folder in /lib/editor/atto/plugins in the moodle directory;
 ```
  **or**
  ```
  - Download the moodle_atto_justify.zip directly
  - O Moodle irar guiá-lo na instalação;
  ```
 - Go to Site `Administration `> `Plugins ` > `Text Editors` > Atto `Toolbar Settings` and you should see the Justify align module, added to your list of installed modules;

 - Now you need to add the name of the justify module configuration, to the menu structure, at the bottom of the page in: `Toolbar Configuration.`

**When you see:**

 ```align = align. ```

 **Change to:**

 ```align = align, justify. (justify is the name of the configuration module)```

- Lastly, make sure that the Justify text icon is on the edit toolbar for atto.

## Development

- o add new functions to the module you need to add them to the `button.js` file located at: `/lib/editor/atto/plugins/justify/yui/src/button/js`

- Once you have made the changes, it is necessary that you run the `shifter` to update the atto

- Here's how to install the [shifter](http://docs.moodle.org/dev/YUI/Shifter).

- After the installation run "sudo shifter" inside the directory: yui/src/button

Make sure the new icon is on the edit toolbar for atto.
