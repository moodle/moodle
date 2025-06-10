Moodle Direct v2
================

Please be aware that the **Develop** branch should not be considered production ready, although it contains the latest fixes and features it may contain bugs. It should be avoided in favour of the **Master** branch which is the latest available branch that has been through the QA process. Please make any pull requests you would like to make to the develop branch.

To see what has changed in recent versions of Moodle Direct V2, see the [CHANGELOG](https://github.com/turnitin/moodle-mod_turnitintooltwo/blob/master/CHANGELOG.md).

If you are having issues, please consult our [TROUBLE SHOOTING](https://github.com/turnitin/moodle-mod_turnitintooltwo/blob/master/TROUBLESHOOTING.md) page.

Installation
------------

Before installing these plugins firstly make sure you are logged in as an Administrator and that you are using Moodle 3.5 or higher.

To install, all you need to do is copy all the files into the mod/turnitintooltwo directory on your moodle installation. You should then go to `"Site Administration" > "Notifications"` and follow the on screen instructions.

To configure the plugin go to `"Site Administration" > "Plugins" > "Activity Modules" > "Turnitin Assignment 2"` and enter your Turnitin account Id, shared key and API URL.

**Note:** *The API connection URL is different for this package to previous Turnitin plugins. It should be https://api.turnitin.com, https://api.turnitinuk.com. or https://sandbox.turnitin.com.*

Testing
-------

Please see the [testing instructions](./TESTING.md) for detail instructions on running the unit tests.

Contributions
------------------------------

We welcome contributions to all elements of the plugin. Since launch we've had a number of users provide us with fixes and enhancements through either pull request or commits linked to from the issues page. If you are interested in contributing enhancements, or would like to solve an issue raised by another user, please feel free to make a pull request against the develop branch.

------------------------------