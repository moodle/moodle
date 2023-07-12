moodle-block_use_stats
======================

Time based use stats block

Version 2016051700
=======================
Adds per instance visibility control to students.
Adds a "onesessionpercourse" global option. forcing working sessions to be splitted when course
context changes in log track.
Unifies time formatting with central block_usestats_format_time function @see mod_learningtimecheck and
report_trainingsessions

Version 2015121900
=======================
Adding keepalive event handling

Calling the use_stats notification handler :

the notification handler allows all pages of Moodle in user agent to trigger a 10 min (adjustable)
request to punch a log event in th user log track.

invoking the notification hook needs to be present on every page of Moodle, so the good
way to implement it is to customize the core site renderer. Another alternative way could be to add 
the use_stats plug into a generic footer include in theme layouts.

class theme_customtheme_core_renderer extends theme_core_renderer {

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
}

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