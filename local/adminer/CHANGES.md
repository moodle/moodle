## Release notes

### Version 4.17.2 (2025031700)
* Switch to new Adminer fork adminneo [Releases notes](https://github.com/adminneo-org/adminneo/releases/tag/v4.17.2) (#23)
* Remove modal feature to simplify the code
* Add a "Back to Moodle" button on the left top corner

### Version 4.8.4.2 (2025021700)
* Fix css problem (#22)
* Make github actions more restrictive

### Version 4.8.4.1 (20241027)
* Add missing capability check (#20)

### Version 4.8.4 (20241025)
* New version of Adminer [Releases notes](https://github.com/adminerevo/adminerevo/releases/tag/v4.8.4)
* Add option to show a quick link in the navigation bar
* Add small button to the modal header to open the content in a new tab without frame.
* Changes the language of the Adminer ui suitable to the language of the current user.

### Version 4.8.3 (20231219)
* New version of Adminer [Releases notes](https://github.com/adminerevo/adminerevo/releases/tag/v4.8.3)
* MBS-8480: Fix modal width on error output (#16)
* Change the modal height to 100%, so on mobile divices it is displayed in fullscreen.
* Add an additional check for a setting in config.php "$CFG->local_adminer_secret".

### Version 4.8.2 (2023112700)
* Due to the lag of updates now it is based on AdminerEvo a new fork of the original Adminer
    For more infos see here: https://github.com/adminerevo/adminerevo
* Removed the "Logout" button
* Apply new coding style
* Adjust github actions

### Version 4.8.1-2 (2022112700 mdl-39)
* Add github actions
* Add simple behat test
* Fix some coding style errors
* Tested with Moodle 4.1

### Version 4.8.1 (2021051700 mdl-39)
* it is based on adminer-4.8.1

### Version 4.7.9 (2021030801 mdl-39)
* Version 4.8.0 was throwing errors

### Version 4.8.0 (2021030701 mdl-39)
* increase version

### Version 4.7.8 (2020120601 mdl-39)
* increase version

### Version 4.7.8 (2020120600 mdl-39)
* it is based on adminer-4.7.8

### Version 2020060100
* Fixes for Moodle 3.9
* This version works with Moodle 3.9

### Version 2020060100
* it is based on adminer-4.7.7
* This version works with Moodle 3.5, 3.6, 3.7, 3.8

### Version 2020020400
* it is based on adminer-4.7.6
* small changes for coding style
* This version works with Moodle 3.5, 3.6, 3.7, 3.8

### Version 2020011200
* it is based on adminer-4.7.5
* This version works with Moodle 3.5, 3.6, 3.7, 3.8

### Version 2019100900
* adjustments for moodle 3.8

### Version 2019090900
* it is based on adminer-4.7.3

### Version 2019081001
* add icons based on moodle icons

### Version 2019081000
* it is based on adminer-4.7.2
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt
* now it uses the css from current theme.

### Version 2019042500
* fix visibility problem on old bootstrap based themes
* change maturity to MATURITY_STABLE

### Version 2019040700
* it is based on adminer-4.7.1
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt
* optional open the current moodle database on start up

### Version 2018102100
* compatible with moodle_35
* it is based on adminer-4.6.3
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt
* slightly different handling of authentication
* better support of php 7.2

### Version 2018040700
* compatible with moodle_35
* replaced grey box with bootstrap modal dialog
* add a legacy css to work on themes not based on boost

### Version 2018040700
* compatible with moodle_31, moodle_33 and moodle_34
* it is based on adminer-4.6.2
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt
* changed design back to default

### Version 2017052800
* compatible with moodle_31, moodle_32 and moodle_33
* it is based on adminer-4.3.1
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt
* export feature in postgresql is working again
* higher z-index value for overlay window
* changed design to price/adminer.css

### Version 2016122600
* compatible with moodle_30, moodle_31 and moodle_32
* it is based on adminer-4.2.5
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt

### Version 2016102000
* CONTRIB-6566 local_adminer: Hardening the Adminer for Moodle

### Version 2016061000
* compatible with moodle_30 and moodle_31
* compatible with php7
* it is based on adminer-4.2.4
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt

### Version 2015052700
* it is based on adminer-4.2.1
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt

### Version 2014111800
* it is based on adminer-4.1.0
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt

### Version 2014011602
* ports other than default port are supported now - thanks to Ian Tasker

### Version 2014011601
* it is based on adminer-4.0.2
    now it doesn't use deprecated ereg functions anymore
    for more infos see here: https://github.com/vrana/adminer/blob/master/changes.txt

### Version 2013061301
* it is based on adminer-3.7.1-dev
    look here: https://github.com/vrana/adminer/blob/master/changes.txt

### Version 2013031601
* it is based on adminer-3.6.4-dev
* it uses the context_system class since moodle 2.2

### Version 2012091801
* added support for MSSQL 2008 R2

### Version 2012060301
* added missing lib/adminer.css

### Version 2012060301
* it is based on adminer-3.4.0-dev
* now it works correctly again in google chrome
* the query textarea can syntax highlighting
