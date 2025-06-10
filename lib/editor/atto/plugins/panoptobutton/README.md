##Prerequisites for using the Panopto Button plugin for Atto:

* Access to the server where Moodle is installed and running. As this plugin is not registered for automatic update, it cannot be upgraded from Moodle UI and requires direct access to the server.
* The Moodle 2.0 Plugin for Panopto. The plugin and instructions for installation may be found at 
  https://github.com/Panopto/Moodle-2.0-Plugin-for-Panopto
* The Atto rich text editor for Moodle. This may be enabled by navigating to **Site Administration > Plugins 
> Text editors > Manage editors** on the left-hand column of the Moodle page and using the up/down arrows to 
move 'Atto HTML editor' to the top of the list. Make sure that Atto is enabled by confirming that its corresponding 
icon under the 'Enable' column shows an unobstructed eye, or click the icon until it appears this way.


##Installation instructions:

1. Get the latest release source package (either as zip or tar.gz) from https://github.com/Panopto/Panopto-Button-for-Atto/releases
1. Log in to Moodle server.
1. (Upgrade only) delete /lib/editor/atto/plugins/panoptobutton
1. Extract source package under /lib/editor/atto/plugins. "Panopto-Button-for-Atto-master" directory should be created.
1. Rename "Panopto-Button-for-Atto-master" to "panoptobutton".
1. Access Moodle home page in a browser while logged in as an administrator. Moodle should detect the new/upgraded plug-in and show "Plugins check" screen which prompts you to install/upgrade. Click  "Upgrade Moodle database now" button to proceed.
1. After installation is completed, visit **Site Administration > Plugins > Text Editors > Atto Toolbar Settings** and confirm that 'PanoptoButton' has been added to the list of installed plugins for Atto.
1. On this same page, add 'panoptobutton' to the list of buttons following the heading 'style1' (it should now read 'style1 = title, bold, italic, widget, panoptobutton') and save changes.
1. Under the 'Site Administration' tab on the left side of the screen, navigate to Security -> Site Policies and check the box next to 'Enable Trusted Content'. By default, trusted content (e.g. Panopto Videos) will be able to be posted by those assigned the role Manager or Course Creator. Trusted roles may be added under **Site Administration > Permissions > Users > Define Roles** by selecting a role and enabling or disabling 'Enable Trusted Content' for that role.
1. Navigate to **Site Administration > Users > Permissions > Define Roles** and assign whichever users should be able to post Panopto videos one of the roles in which posting trusted content is enabled.
1. The Panopto button for Atto should now be installed and will appear on all rich text input windows throughout the Moodle site.
    
##Usage:

1. On a rich text input window on the Moodle site, navigate the cursor to the position at which you would like your video to be inserted and click the Panopto button (the button containing the green Panopto logo). 
1. If the current editor is inside a course provisioned to a Panopto server, the selection screen that appears will automatically display videos from the server and folder to which the current course was provisioned. If outside of a course page, or in a non-provisioned course, you will have the ability to select folders from the default server specified during the plugin's installation.
1. Select one or more videos to insert by checking the boxes next to their thumbnail images. When one or more videos is selected, and 'Insert' button will appear in the bottom left-hand corner of the selection window. Clicking this button will close the selection window and insert the selected videos into the editor at the point selected, positioning multiple videos directly on top of one another by default.
1. Videos in the editor can be positioned like text objects, using the space, tab, or enter keys with the cursor positioned behind them to  change their position in the editor, or the backspace key with the cursor positioned ahead of them to remove them.
1. To resize a video, click 'Show more buttons' in the editor toolbar (this should be the leftmost button), and then click the 'HTML' button that appears farthest to the right (this button's icon will look like '**< >**'). The content in the editor will change to it's HTML representation. Videos in this view will appear as strings in the form:

```
<object type='text/html' data='http://demo.hosted.panopto.com/Panopto/Pages/Viewer.aspx?id=7c4afd6d-6e43-4b8e-bde9-425b25e5940f&amp;v=1' width="450" height="300" frameborder='0'>
```

*(Note that object embedding may not work on older browsers)*

By changing the values corresponding to the 'width' and 'height' fields, the size of the embedded video may be changed. The changes in size may be previewed by clicking the 'HTML' button again to return to the formatted view.

##Making changes to the code:

Any changes to the plugin code within 'button.js' must be processed using YUI Shifter found at https://github.com/yui/shifter. To use the shifter, first install it by following the instructions on the linked page. Then, after saving any changes to 'button.js', open a terminal or command prompt window and navigate to the directory within the Panopto Button folder containing the file 'build.json' (this should be yui/src/button) and run the 'shifter' command. After the script confirms that it executed successfully, reinstall the plugin by first uninstalling the current active version via **Site Administration > Plugins > Text Editors > Atto Toolbar Settings** on the Moodle page (making sure you keep a copy of the newly modified directory), and then following the above installation instructions, using the directory containing the new, processed code.
    
