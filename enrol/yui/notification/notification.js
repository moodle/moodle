/**
 * This module provides backwards compatability and should be removed
 * entirely in Moodle 2.5
 */
YUI.add('moodle-enrol-notification', function(Y) {
    console.log("You are using a deprecated name. Please update your YUI module to use moodle-core-notification instead of moodle-enrol-notification");
}, '@VERSION@', {requires:['base','node','overlay','event-key', 'moodle-core-notification']});
