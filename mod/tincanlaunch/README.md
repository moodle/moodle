xAPI Launch Link
============

A Moodle plug-in that allows the launch of xAPI content using a separate LRS. 

## Background
The plugin is called tincanlaunch because of the original research project called 'Project Tin Can'. At this point in time, the more commonly heard names of Tincan are the 'Experience API' and 'xAPI'. More details can be found here: https://xapi.com/tin-can-experience-api-xapi

One of the key issues in xAPI is launching content in such a way that the activity provider knows:
* the LRS endpoint
* authorization credentials
* user information


This project will utilize the most common launch method:
* [Rustici Launch Method](https://github.com/RusticiSoftware/launch/blob/master/lms_lrs.md)

A second method will be considered as development continues, [cmi5](http://aicc.github.io/CMI-5_Spec_Current/). 
 

## What you will need
To use this plugin, you will need the following:
* A working instance of a [supported Moodle version](https://docs.moodle.org/dev/Releases) 3.9+
* A [supported PHP version](https://www.php.net/supported-versions.php) (as of this writing, the supported versions of PHP are 7.4, 8.0, and 8.1)
* Moodle administrative access
* Web-accessible xAPI-compliant content that implements the launch mechanism outlined above (Articulate Storyline, Adobe Captivate, etc.)
* An xAPI-compliant LRS (LearningLocker, Watershed, SCORM Cloud)

## Installation (Recommended)
It is recommended to get this plugin from the Moodle Plugins Database (https://moodle.org/plugins/mod_tincanlaunch)

This plugin is installed in the same way as any activity plugin. Download the zip file and navigate to Moodle System administration > Plugins > Install plugins.

### Course set up
This plugin can be added to a course like any other course activity. Simply add an activity and select xAPI Launch Link from the list.

There are appearance settings that allow various launch settings.
* Simplified Launch - When learners click the activity from within the Moodle course, they will automatically be brought to the xAPI content. This bypasses the registration screen. Most browsers will block this launch, and the learner must allow popups from the site.
* Single Registrations - When learners click the activity, they will be brought to a registration screen. If they have no prior registrations, they can create a new one. Subsequent visits to the activity will allow the user to launch the existing activity.
* Multiple Registrations - This differs from 'Single Registrations' in that the user will be able to see and launch all of their prior registrations as well as launch a new registration.

The settings for this module all have help text which can be accessed by clicking the '?' icon next to that setting. 

## Using the plugin
Depending on the settings chosen during the activity setup, the learner will either directly launch the content (Simplified Launch) or be brought to a Registrations page (Single/Multiple Registrations).

Moodle will pass the xAPI content a registration ID as a universal unique ID (UUID) representing the previous attempt or a newly generated one for a new attempt. It's up to the xAPI content what it does with that data, but ideally, it will store its bookmarking state on a per-registration basis.

Note that the list of attempts is stored in the LRS, rather than Moodle, and can therefore be read and modified by another LMS or by the learning activity itself. Additionally, if another copy of the launch link is installed elsewhere on Moodle or even on another Moodle, the data will be shared so long as the user email and activity ID are the same.

## FAQ

### Where does the tracking data go?
Tracking data from the learning activity is stored in your LRS and can be retrieved and viewed using an xAPI-compliant reporting tool.


### Why doesn't the plugin do x, y, or z?
If you'd like the plugin to do something, please raise an issue; perhaps someone within the community will develop it for you. If you want to make sure it happens or is done quickly, please email [david.pesce@exputo.com](mailto:david.pesce@exputo.com) if you'd like to hire us.


## Reporting issues
Please report any issues with this plugin here: https://github.com/davidpesce/moodle-mod_tincanlaunch/issues
Please provide screenshots of your settings (both at the plugin and instance level) and a link to your content. 

The majority of issues are caused by incorrect settings. You can see previously closed issues here: https://github.com/davidpesce/moodle-mod_tincanlaunch/issues?q=is%3Aissue+is%3Aclosed
