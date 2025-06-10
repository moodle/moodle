##Instructions for installing the Panopto button for the TinyMCE editor in Moodle:

*--Installation Note: This plugin is intended to be installed along with the Panopto plugin for Moodle, which can be found at:
  https://github.com/Panopto/Moodle-2.0-Plugin-for-Panopto

*The Panopto plugin is pre-requisite for installing and running the Panopto button for TinyMCE

1. Copy the entire "panoptobutton" directory into TinyMCE's plugin directory at
  <Moodle Server>/moodle/lib/editor/tinymce/plugins

2. Navigate to your Moodle server within a browser (refresh the page if it is already open) and
  follow the instructions to complete the plugin's installation.

3. From the Moodle server in your browser navigate to the "Add Panopto Video" plugin settings under
  Site Administration -> Plugins -> Text Editors -> TinyMCE -> Add Panopto Video.

    Enter the Panopto Server Address
    Note: Please include the protocol and match whether your site uses 'http' or 'https'.
    Example is 'https://demo.hosted.panopto.com".

*The plugin should now be installed and the Panopto button will appear in all TinyMCE editor windows throughout the server.
