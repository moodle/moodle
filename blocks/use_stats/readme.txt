####################################
#
#    Moodle Third Party Adds-on
#
####################################
#
# module: Use Stats Block
# type: block
# whouses: teachers, eventually students
# developer: Valery Fremaux (valery.Fremaux@club-internet.fr)
# date: 2016/02/23
# Version : Moodle 2

## 20160121

Adding cleanup task to track log cleanups
Adding indexes on mdl_block_use_stats_log

Install the new keepalive hook in your theme :

Basic snippet of code to inject the keep_alive use_stats events
is : 

    // use_stats notification plug / VF Consulting 2015-12-19
    if (file_exists($CFG->dirroot.'/blocks/use_stats/lib.php')) {
        include_once $CFG->dirroot.'/blocks/use_stats/lib.php';
        $str .= block_use_stats_setup_theme_notification();
    }

You may implement it in several ways in Moodle : 

1. Overriding a core renderer (in a theme_yourtheme_core_renderer class):

The following function will override the native one from Moodle and inject
the keep_alive pattern at bottom of every moodle page.

    function standard_end_of_body_html() {
        global $CFG;

        $str = '';

        // use_stats notification plug / VF Consulting 2015-12-19
        if (file_exists($CFG->dirroot.'/blocks/use_stats/lib.php')) {
            include_once $CFG->dirroot.'/blocks/use_stats/lib.php';
            $str .= block_use_stats_setup_theme_notification();
        }
        $str .= parent::standard_end_of_body_html();
        return $str;
    }

2. Directly in layouts : 

If you have in your layout a footer sequence, then add the given php
snippet in the top of the footer HTML code.


##

This block allows displaying use stats for the current user.

Use stats are given as an answer to the "how many time I spent on this Moodle". This may be usefull in situation where work time should be known to estimate average personnal productivity and enhancing its own process. 

In other situations could it be used as an objetive proof of real work in some extreme and conflictual situations we hope they never occur.

## Short overview ##

This block samples the user's log records and thresholds the activity backtrace. The main hypothesis is that any activity type unless offline activity or in-classroom activity may underlie a constant loggin track generation. 

The block compiles all log events and summarizes all intervals larger than an adjustable threshold. Compilation are also made on a course basis.

The more Moodle is used as a daily content editing tool, the more accurate should be this report.

## Install the block ##

unzip this distribution in the <MOODLE_INSTALL>/blocks folder

browse to the "administration" page to make the block registered by Moodle.

## Use the block ##

Add a block in any workspace you use. Compilation will be visible to the current user, with no restrictions if he is a teacher. 

Students may be given access to their own report, if the instance is programmed for by the teacher within a course context, or by the administrator out of a course context (MyMoodle, general pages)

2017022102 - New distribution policy
====================================

Due to the huge amount of demands for increasing reliability and accuracy of the time tracking tools in Moodle, and
the huge amount of work hours that it needs, we have decided to split our publication policy keeping the main essential functions
in the community version of the plugin, and deferring the most demanding, test and validation consuming, or advanced features into
a "pro" version. We hope usual users of such "dual release" plugins will understand our need to stay being able to maintain and pursue
provide support, innovation and code quality for Moodle, but being also supported ourself to do so. Plugin documentation provides
information about feature sets and how to get "pro" versions. In our scope is also to provide simpler plugins for the usual non advanced use,
and keep very fine options to people that really needs them.

Policy change will not affect the moodle version path of the plugin, data model remains unchanged, such as core bound definitions.