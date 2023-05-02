# Moodle Plugins for Microsoft Services
*including* **Microsoft 365** *and other Microsoft services*

## Microsoft 365 Local Plugin

This plugin is a shell plugin that has dependencies on all Microsoft 365 plugins. This helps keep related plugins together.

This plugin requires all Microsoft 365 plugins:
  - [moodle-auth_oidc](https://github.com/Microsoft/moodle-auth_oidc)
  - [moodle-block_microsoft](https://github.com/Microsoft/moodle-block_microsoft)
  - [moodle-local_o365](https://github.com/Microsoft/moodle-local_o365)
  - [moodle-repository_office365](https://github.com/Microsoft/moodle-repository_office365)
  - [moodle-filter_oembed](https://github.com/Microsoft/moodle-filter_oembed)


This is part of the suite of Microsoft 365 plugins for Moodle.

This repository is updated with stable releases. To follow active development, see: https://github.com/Microsoft/o365-moodle

## Installation

1. Unpack the plugin into /local/office365 within your Moodle install.
2. From the Moodle Administration block, expand Site Administration and click "Notifications".
3. Follow the on-screen instuctions to attempt to install the plugins.
4. You'll see a list of missing dependencies needed to complete the installation. Each of these is also available from Github at the links above. Install each of the dependencies. When complete, you'll have the entire set of plugins installed and this plugin's install can complete.

For more documentation, visit https://docs.moodle.org/34/en/Office365

For more information including support and instructions on how to contribute, please see: https://github.com/Microsoft/o365-moodle/blob/master/README.md

## Copyright

&copy; Microsoft, Inc.  Code for this plugin is licensed under the GPLv3 license.

Any Microsoft trademarks and logos included in these plugins are property of Microsoft and should not be reused, redistributed, modified, repurposed, or otherwise altered or used outside of this plugin.
