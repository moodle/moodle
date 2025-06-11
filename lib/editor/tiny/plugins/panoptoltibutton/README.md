# moodle-tiny_panoptoltibutton
This plugin adds a button to the TinyMCE Text Editor to allow for teachers and students to embed Panopto content using existing Panopto LTI tools


## Prerequisites for using the Panopto Button plugin for TinyMCE:

* Access to the server where Moodle is installed and running. As this plugin is not registered for automatic update, it cannot be upgraded from Moodle
UI and requires direct access to the server.
* The Moodle 2.0 Plugin for Panopto. The plugin and instructions for installation may be found at
  https://github.com/Panopto/Moodle-2.0-Plugin-for-Panopto
* The TinyMCE rich text editor for Moodle. This may be enabled by navigating to **Site Administration > Plugins > Text editors > Manage editors** on
the left-hand column of the Moodle page and using the up/down arrows to move 'TinyMCE editor' to the top of the list. Make sure that TinyMCE is
enabled by confirming that its corresponding icon under the 'Enable' column shows an unobstructed eye, or click the icon until it appears this way.


## Installation instructions:

1. Get the latest release source package (either as zip or tar.gz) from https://github.com/Panopto/moodle-tiny_panoptoltibutton/releases
1. Log in to Moodle as an Admin.
1. Navigate to **System Administration > Plugins > Install Plugins**
1. Drag the .zip of the latest release over the drag and drop section to install a plugin.
1. Moodle should detect the new/upgraded plug-in and show "Plugins check" screen which prompts you to install/upgrade. Click  "Upgrade Moodle database now" button to proceed.
1. After installation is completed, visit **Site Administration > Plugins > Text Editors > General Settings** and confirm that 'Add Panopto Video' has been added to the list of installed plugins for TinyMCE.
1. The Panopto LTI button for TinyMCE should now be installed and will appear on all rich text input windows throughout the Moodle site.

## Usage:

1. On a rich text input window on the Moodle site, navigate the cursor to the position at which you would like your content to be inserted and click the Panopto dropdown button (the button containing the green Panopto logo and a dropdown icon).
1. We only show existing LTI tool that have a Tool URL that matches the Panopto server a course is provisioned with. At this time Panopto only supports embedding rich text editor LTI content from pages associated with a course provisioned by the Panopto block.
1. After selecting an item in the dropdown a new window will appear containing the selected Panopto LTI tool. From here the user can select and embed content depending on what LTI tool was selected.

## Making changes to the code:

### Update AMD module

You can build all modules in Moodle by using the grunt amd command. To update the amd module from this plugin:
1. Execute `npm install` on the root of the Moodle project.
2. Navigate to `<moodle-root>/lib/editor/tiny/panoptoltibutton/amd/` and execute:

```
$ npx grunt amd
```

### Development mode

In development mode Moodle will also send the browser the corresponding source map files for each of the JavaScript modules. The source map files will tell the browser how to map the minified source code back to the un-minified original source code so that the original source files will be displayed in the sources section of the browser's development tools.

To enable development mode set the cachejs config value to false in the admin settings or directly in your config.php file:

```
// Prevent JS caching
$CFG->cachejs = false;
```

## Deploy new version

1. Increment the version in version.php by 1 (e.g. change 2020061000 to 2020061001)
1. Add everything except the .git and node_modules folder to a zip archive
1. Treat the zip archive as a new release and follow the installation instructions above. As long as the version is higher than the currently installed version an update should trigger.