# Moodle Plugins for Microsoft Services
*including* **Office 365** *and other Microsoft services*

## OpenID Connect Authentication Plugin.

The OpenID Connect plugin provides single-sign-on functionality using configurable identity providers.

This is part of the suite of Office 365 plugins for Moodle.

This repository is updated with stable releases. To follow active development, see: https://github.com/Microsoft/o365-moodle

## Installation

1. Unpack the plugin into /auth/oidc within your Moodle install.
2. From the Moodle Administration block, expand Site Administration and click "Notifications".
3. Follow the on-screen instuctions to install the plugin.
4. To configure the plugin, from the Moodle Administration block, go to Site Administration > Plugins > Authentication > Manage Authentication.
5. Click the icon to enable the plugin, then visit the settings page to configure the plugin. Follow the directions below each setting.

For more documentation, visit https://docs.moodle.org/30/en/Office365

## Support

If you are experiencing problems, have a feature request, or have a question, please open an issue on Github at https://github.com/Microsoft/o365-moodle.

To help developers debug problems, please include the following in all issues:
- Plugin versions.
- Moodle version.
- Detailed instructions of what went wrong and how to reproduce the problem.
- Any error messages encountered.
- PHP version.
- Database software and versions.
- Any other environmental information available.

Note that developers will triage issues and deal with more serious problems first. All issues will be addressed but some may not be addressed immediately.

## Contributing

We're looking for community contributions! Feel free to submit pull requests, but please do so against the development repository at https://github.com/Microsoft/o365-moodle. Pull requests submitted to individual plugin repositories cannot be accepted.

### Needed Contributions
Smaller issues that developers cannot address right away will be labeled with "Help Wanted" in the issue tracker in the development repository at https://github.com/Microsoft/o365-moodle/issues. These are only suggestions - we can also accept pull requests fixing other bugs, or even adding new features.

Pull requests adding new features are much appreciated but note that they may be rejected (even if technically sound) if they do not match the direction of the project. If you want to add a new feature, it's best to open an issue outlining your idea first, and get feedback from the maintainers.

Contributions to our documentation are especially appreciated! All documentation lives in the /local/o365docs folder of the development repository (https://github.com/Microsoft/o365-moodle). Updates to this documentation can be sent via pull request like any other contributions.

### Code Review
All pull requests go through a thorough examination from developers before they are merged. Please read our [code review process](https://github.com/Microsoft/o365-moodle/tree/master/local/o365docs/codereview.md) and ensure your code is consistent before submitting. A developer may respond with changes that are needed before a pull request can be accepted and it is up to the submitter to make those changes. If accepted, your commit will remain as-is to ensure you get credit, but developers may modify solutions slightly in subsequent commits.

### CLA
Finally, before we can accept your pull request, you'll need to electronically complete Microsoft's [Contributor License Agreement](https://cla.microsoft.com/). If you've done this for other Microsoft projects, then you're already covered.

[Why a CLA?](https://www.gnu.org/licenses/why-assign.html) (from the FSF)

## Copyright

&copy; Microsoft, Inc.  Code for this plugin is licensed under the GPLv3 license.

Any Microsoft trademarks and logos included in these plugins are property of Microsoft and should not be reused, redistributed, modified, repurposed, or otherwise altered or used outside of this plugin.
