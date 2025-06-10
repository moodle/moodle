##Prerequisites for using the vidgrid Button plugin for Atto:

* Access to the server where Moodle is installed and running. As this plugin is not registered for automatic update, it cannot be upgraded from Moodle UI and requires direct access to the server.
* The Atto rich text editor for Moodle. This may be enabled by navigating to **Site Administration > Plugins > Text editors > Manage editors** on the left-hand column of the Moodle page and using the up/down arrows to 
move 'Atto HTML editor' to the top of the list. Make sure that Atto is enabled by confirming that its corresponding icon under the 'Enable' column shows an unobstructed eye, or click the icon until it appears this way.


##Installation instructions:

1. Get the latest release source package (either as zip or tar.gz) from https://github.com/ilosvideos/moodleAttoPlugin/releases
1. Log in to Moodle server.
1. (Upgrade only) delete /lib/editor/atto/plugins/vidgridbutton
1. Extract source package under /lib/editor/atto/plugins. "vidgrid-Button-for-Atto-master" directory should be created.
1. Rename "vidgrid-Button-for-Atto-master" to "vidgridbutton".
1. Access Moodle home page in a browser while logged in as an administrator. Moodle should detect the new/upgraded plug-in and show "Plugins check" screen which prompts you to install/upgrade. Click  "Upgrade Moodle database now" button to proceed.
1. After installation Moodle is going to redirect you to the plugin settings page. Setup your Organization API key in the Textbox. You can find your Organization API key in the vidgrid web app. Important: Plugin won't work until you set up your Organization API key' 
1. After previous step installation is completed, visit **Site Administration > Plugins > Text Editors > Atto Toolbar Settings** and confirm that 'vidgridButton' has been added to the list of installed plugins for Atto.
1. On this same page, add 'vidgridbutton' to the list of buttons following the heading 'style1' (it should now read 'style1 = title, bold, italic, widget, vidgridbutton') and save changes.
1. Under the 'Site Administration' tab on the left side of the screen, navigate to Security -> Site Policies and check the box next to 'Enable Trusted Content'. By default, trusted content (e.g. vidgrid Videos) will be able to be posted by those assigned the role Manager or Course Creator. Trusted roles may be added under **Site Administration > Permissions > Users > Define Roles** by selecting a role and enabling or disabling 'Enable Trusted Content' for that role.
1. Navigate to **Site Administration > Users > Permissions > Define Roles** and assign whichever users should be able to post vidgrid videos one of the roles in which posting trusted content is enabled.
1. The vidgrid button for Atto should now be installed and will appear on all rich text input windows throughout the Moodle site.
    
##Usage:

1. On a rich text input window on the Moodle site, navigate the cursor to the position at which you would like your video to be inserted and click the vidgrid button (the button containing the blue vidgrid logo). 
1. Login, into your vidgrid account.
1. Search the video you want.
1. Click the video and it should be inserted as an iframe in the rich text editor 

[Installation and usage video tutorial](https://app.vidgrid.com/view/ugE2N2Ee1lOf)

*(Note that object embedding may not work on older browsers)*

By changing the values corresponding to the 'width' and 'height' fields, the size of the embedded video may be changed. The changes in size may be previewed by clicking the 'HTML' button again to return to the formatted view.

##Making changes to the code:

Any changes to the plugin code within 'button.js' must be processed using YUI Shifter found at https://github.com/yui/shifter. To use the shifter, first install it by following the instructions on the linked page. Then, after saving any changes to 'button.js', open a terminal or command prompt window and navigate to the directory within the vidgrid Button folder containing the file 'build.json' (this should be yui/src/button) and run the 'shifter' command. After the script confirms that it executed successfully, reinstall the plugin by first uninstalling the current active version via **Site Administration > Plugins > Text Editors > Atto Toolbar Settings** on the Moodle page (making sure you keep a copy of the newly modified directory), and then following the above installation instructions, using the directory containing the new, processed code.
    
