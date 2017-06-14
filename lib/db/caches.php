<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Core cache definitions.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 * It contains the components that are requried in order to use caching.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$definitions = array(

    // Used to store processed lang files.
    // The keys used are the revision, lang and component of the string file.
    // The static acceleration size has been based upon student access of the site.
    'string' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 30,
        'canuselocalstore' => true,
    ),

    // Used to store cache of all available translations.
    'langmenu' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'canuselocalstore' => true,
    ),

    // Used to store database meta information.
    // The database meta information includes information about tables and there columns.
    // Its keys are the table names.
    // When creating an instance of this definition you must provide the database family that is being used.
    'databasemeta' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'requireidentifiers' => array(
            'dbfamily'
        ),
        'simpledata' => true, // This is a read only class, so leaving references in place is safe.
        'staticacceleration' => true,
        'staticaccelerationsize' => 15
    ),

    // Event invalidation cache.
    // This cache is used to manage event invalidation, its keys are the event names.
    // Whenever something is invalidated it is both purged immediately and an event record created with the timestamp.
    // When a new cache is initialised all timestamps are looked at and if past data is once more invalidated.
    // Data guarantee is required in order to ensure invalidation always occurs.
    // Persistence has been turned on as normally events are used for frequently used caches and this event invalidation
    // cache will likely be used either lots or never.
    'eventinvalidation' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'staticacceleration' => true,
        'requiredataguarantee' => true,
        'simpledata' => true,
    ),

    // Cache for question definitions. This is used by the question_bank class.
    // Users probably do not need to know about this cache. They will just call
    // question_bank::load_question.
    'questiondata' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true, // The id of the question is used.
        'requiredataguarantee' => false,
        'datasource' => 'question_finder',
        'datasourcefile' => 'question/engine/bank.php',
    ),

    // HTML Purifier cache
    // This caches the html purifier cleaned text. This is done because the text is usually cleaned once for every user
    // and context combo. Text caching handles caching for the combination, this cache is responsible for caching the
    // cleaned text which is shareable.
    'htmlpurifier' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'canuselocalstore' => true,
    ),

    // Used to store data from the config + config_plugins table in the database.
    // The key used is the component:
    //   - core for all core config settings
    //   - plugin component for all plugin settings.
    // Persistence is used because normally several settings within a script.
    'config' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'staticacceleration' => true,
        'simpledata' => true
    ),

    // Groupings belonging to a course.
    // A simple cache designed to replace $GROUPLIB_CACHE->groupings.
    // Items are organised by course id and are essentially course records.
    'groupdata' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true, // The course id the groupings exist for.
        'simpledata' => true, // Array of stdClass objects containing only strings.
        'staticacceleration' => true, // Likely there will be a couple of calls to this.
        'staticaccelerationsize' => 2, // The original cache used 1, we've increased that to two.
    ),

    // Used to cache calendar subscriptions.
    'calendar_subscriptions' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
    ),

    // Cache the capabilities list DB table. See get_all_capabilities in accesslib.
    'capabilities' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 1,
        'ttl' => 3600, // Just in case.
    ),

    // YUI Module cache.
    // This stores the YUI module metadata for Shifted YUI modules in Moodle.
    'yuimodules' => array(
        'mode' => cache_store::MODE_APPLICATION,
    ),

    // Cache for the list of event observers.
    'observers' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 2,
    ),

    // Cache used by the {@link core_plugin_manager} class.
    // NOTE: this must be a shared cache.
    'plugin_manager' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
    ),

    // Used to store the full tree of course categories.
    'coursecattree' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'staticacceleration' => true,
        'invalidationevents' => array(
            'changesincoursecat',
        )
    ),
    // Used to store data for course categories visible to current user. Helps to browse list of categories.
    'coursecat' => array(
        'mode' => cache_store::MODE_SESSION,
        'invalidationevents' => array(
            'changesincoursecat',
            'changesincourse',
        ),
        'ttl' => 600,
    ),
    // Used to store data for course categories visible to current user. Helps to browse list of categories.
    'coursecatrecords' => array(
        'mode' => cache_store::MODE_REQUEST,
        'simplekeys' => true,
        'invalidationevents' => array(
            'changesincoursecat',
        ),
    ),
    // Cache course contacts for the courses.
    'coursecontacts' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'staticacceleration' => true,
        'simplekeys' => true,
        'ttl' => 3600,
    ),
    // Used to store data for repositories to avoid repetitive DB queries within one request.
    'repositories' => array(
        'mode' => cache_store::MODE_REQUEST,
    ),
    // Used to store external badges.
    'externalbadges' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'ttl' => 3600,
    ),
    // Accumulated information about course modules and sections used to print course view page (user-independed).
    // Used in function get_fast_modinfo(), reset in function rebuild_course_cache().
    'coursemodinfo' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'canuselocalstore' => true,
    ),
    // This is the session user selections cache.
    // It's a special cache that is used to record user selections that should persist for the lifetime of the session.
    // Things such as which categories the user has expanded can be stored here.
    // It uses simple keys and simple data, please ensure all uses conform to those two constraints.
    'userselections' => array(
        'mode' => cache_store::MODE_SESSION,
        'simplekeys' => true,
        'simpledata' => true
    ),

    // Used to cache activity completion status.
    'completion' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'ttl' => 3600,
        'staticacceleration' => true,
        'staticaccelerationsize' => 2, // Should be current course and site course.
    ),

    // Used to cache course completion status.
    'coursecompletion' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'ttl' => 3600,
        'staticacceleration' => true,
        'staticaccelerationsize' => 30, // Will be users list of current courses in nav.
    ),

    // A simple cache that stores whether a user can expand a course in the navigation.
    // The key is the course ID and the value will either be 1 or 0 (cast to bool).
    // The cache isn't always up to date, it should only ever be used to save a costly call to
    // can_access_course on the first page request a user makes.
    'navigation_expandcourse' => array(
        'mode' => cache_store::MODE_SESSION,
        'simplekeys' => true,
        'simpledata' => true
    ),

    // Caches suspended userids by course.
    // The key is the courseid, the value is an array of user ids.
    'suspended_userids' => array(
        'mode' => cache_store::MODE_REQUEST,
        'simplekeys' => true,
        'simpledata' => true,
    ),

    // Caches plugins existing functions by function name and file.
    // Set static acceleration size to 5 to load a few functions.
    'plugin_functions' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 5
    ),

    // Caches data about tag collections and areas.
    'tags' => array(
        'mode' => cache_store::MODE_REQUEST,
        'simplekeys' => true,
        'staticacceleration' => true,
    ),

    // Grade categories. Stored at session level as invalidation is very aggressive.
    'grade_categories' => array(
        'mode' => cache_store::MODE_SESSION,
        'simplekeys' => true,
        'invalidationevents' => array(
            'changesingradecategories',
        )
    ),

    // Store temporary tables information.
    'temp_tables' => array(
        'mode' => cache_store::MODE_REQUEST,
        'simplekeys' => true,
        'simpledata' => true
    ),

    // Caches tag index builder results.
    'tagindexbuilder' => array(
        'mode' => cache_store::MODE_SESSION,
        'simplekeys' => true,
        'simplevalues' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 10,
        'ttl' => 900, // 15 minutes.
        'invalidationevents' => array(
            'resettagindexbuilder',
        ),
    ),

    // Caches message processors.
    'message_processors_enabled' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 3
    ),

    // Caches the time of the last message between two users.
    'message_time_last_message_between_users' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true, // The id of the sender and recipient is used.
        'simplevalues' => true,
        'datasource' => '\core_message\time_last_message_between_users',
    ),

    // Caches font awesome icons.
    'fontawesomeiconmapping' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 1
    ),

    // Caches processed CSS.
    'postprocessedcss' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => false,
    ),
);
