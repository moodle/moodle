# Panopto-LTI-Button-For-Atto
 This plugin adds a button to the Atto Text Editor to allow for teachers and students to embed Panopto content using existing Panopto LTI tools


## Prerequisites for using the Panopto Button plugin for Atto:

* Access to the server where Moodle is installed and running. As this plugin is not registered for automatic update, it cannot be upgraded from Moodle 
UI and requires direct access to the server.
* The Moodle 2.0 Plugin for Panopto. The plugin and instructions for installation may be found at 
  https://github.com/Panopto/Moodle-2.0-Plugin-for-Panopto
* The Atto rich text editor for Moodle. This may be enabled by navigating to **Site Administration > Plugins > Text editors > Manage editors** on 
the left-hand column of the Moodle page and using the up/down arrows to move 'Atto HTML editor' to the top of the list. Make sure that Atto is 
enabled by confirming that its corresponding icon under the 'Enable' column shows an unobstructed eye, or click the icon until it appears this way.


## Installation instructions:

1. Get the latest release source package (either as zip or tar.gz) from https://github.com/Panopto/Panopto-LTI-Button-for-Atto/releases
1. Log in to Moodle as an Admin.
1. Navigate to **System Administration > Plugins > Install Plugins**
1. Drag the .zip of the latest release over the drag and drop section to install a plugin. 
1. Moodle should detect the new/upgraded plug-in and show "Plugins check" screen which prompts you to install/upgrade. Click  "Upgrade Moodle database now" button to proceed.
1. After installation is completed, visit **Site Administration > Plugins > Text Editors > Atto Toolbar Settings** and confirm that 'panoptoltibutton' has been added to the list of installed plugins for Atto.
1. On this same page, add 'panoptoltibutton' to the list of buttons following the heading 'style1' (it should now read 'style1 = title, bold, italic, widget, panoptoltibutton') and save changes.
1. The Panopto LTI button for Atto should now be installed and will appear on all rich text input windows throughout the Moodle site.

## Usage:

1. On a rich text input window on the Moodle site, navigate the cursor to the position at which you would like your content to be inserted and click the Panopto dropdown button (the button containing the green Panopto logo and a dropdown icon). 
1. The dropdown will only show existing LTI tools that have a Tool URL that matches the Panopto server a course is provisioned with. At this time Panopto only supports embedding rich text editor LTI content from pages associated with a course provisioned by the Panopto block.
1. After selecting an item in the dropdown a new window will appear containing the selected Panopto LTI tool. From here the user can select and embed content depending on what LTI tool was selected. 

## Making changes to the code:

Any changes to the plugin code within 'button.js' must be processed using Grunt Shifter, documentation found at https://docs.moodle.org/dev/Grunt. To use Grunt Shifter please follow the following steps:

1. Install Node.js and npm.
1. Open a terminal and install Grunt `npm install -g grunt`.
1. Navigate the terminal to the root directory of the project.
1. Run `npm install`, this will install all dependencies needed for the grunt shifter command. 
1. After making any changes to button.js open a terminal to the root of the project directory and run 'grunt shifter -v'. -v allows error logs to show for any issues.  
1. Increment the version in version.php by 1 (e.g. change 2020061000 to 2020061001)
1. Add everything except the .git and node_modules folder to a zip archive
1. Treat the zip archive as a new release and follow the installation instructions above. As long as the version is higher than the currently installed version an update should trigger.