Instructions for updating the Tiny Premium plugin for Moodle.

A request to Tiny Cloud is made in the plugin.js file of this plugin.
This request passes the Tiny Premium API key as part of a URL.
The URL also contains the major version of Tiny and may need to be updated.

The URL looks like this: https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/VERSION/plugins.min.js

When upgrading, check Tiny Cloud's documentation regarding the correct API URL
to use. Go to https://www.tiny.cloud/docs/tinymce

TinyMCE Premium plugins can be individually enabled/disabled by admins.
Each release of TinyMCE may have a different selection of plugins available.
When upgrading, please check the list of available TinyMCE Premium plugins and update the list
with the revisions (lib/editor/tiny/plugins/premium/classes/manager.php).
