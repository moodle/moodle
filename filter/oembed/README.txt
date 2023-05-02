Description:
This is a text filter for Moodle that converts urls from many different media sites into embeded content.
Embed code is retrieved from the original site so should work even if the site changes embed format.

Installation:
Download the source files. (zip file is available under download section)
Unzip the package
Copy the "oembed" folder to moodle/filter on the Moodle server.
Login as an admin on the Moodle site and install the filter.

Upgrading from earlier versions:
Upgrade per normal procedures. Your settings from earlier plugins will be preserved.
NOTE - Embed providers may change the text that identifies them. It is possible that media embedded previously on your site no
longer meets the provider text definitions, and as such, may not show up as embedded media. Check the provider definition to see
if the media link needs to change.

To use:
Under Plugins > Filters > Oembed Filter / Settings, you can choose:
  - The type of tag to identify the embedded media.
  - To delay the media loading or load it immediately.
By default the oembed filter disables all providers.
You can change this under Plugins > Filters > Oembed Filter / Manage providers.

When inserting a media link url into a discussion, create a hyperlink and insert the url as the target.
When the discussion is posted the url will be changed into the embed content.
N.B. if you enable the "Convert URLs into links and images" filter ahead of this then it is easier for users to embed media.

The embedded media providers are in three groups:
  - Downloaded from http://oembed.com/providers.json. This is the main repository that manages Oembed provider definitions.
    These are updated regularly in the cron job, and can change.
  - Plugins provided to extend media providers provided in earlier versions of the plugin, but not contained in the provider repo.
  - Local providers which allow a site administrator to save a downloaded one locally, so that it does not change with download
    updates. This also allows new providers to be created that are not part of the omebed repo.
