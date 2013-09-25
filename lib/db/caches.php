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
    // The keys used are the component of the string file.
    // The persistent max size has been based upon student access of the site.
    'string' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 30
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

    // YUI Module cache.
    // This stores the YUI module metadata for Shifted YUI modules in Moodle.
    'yuimodules' => array(
        'mode' => cache_store::MODE_APPLICATION,
    ),

    // Cache for the list of known plugin and subplugin types - {@see get_plugin_types()}.
    // Contains two arrays of (string)pluginname => (string)location. The first array with
    // the key 0 contains locations relative to $CFG->dirroot. The second array with the
    // key 1 contains absolute paths.
    'plugintypes' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true, // 0 or 1 depending on the requested location type.
        'simpledata' => true, // Array of strings.
        'staticacceleration' => true, // Likely there will be a couple of calls to this.
        'staticaccelerationsize' => 2, // Both arrays should stay loaded in memory.
    ),

    // Cache for the list of installed plugins - {@see get_plugin_list()}.
    // The key consists of the plugin type string (e.g. mod, block, enrol etc).
    // The value is an associative array of plugin name => plugin location.
    'pluginlist' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 2,
    ),

    // Cache used by the {@link plugininfo_base} class.
    'plugininfo_base' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 2,
    ),

    // Cache used by the {@link plugininfo_mod} class.
    'plugininfo_mod' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 1,
    ),

    // Cache used by the {@link plugininfo_block} class.
    'plugininfo_block' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 1,
    ),

    // Cache used by the {@link plugininfo_filter} class.
    'plugininfo_filter' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 1,
    ),

    // Cache used by the {@link plugininfo_repository} class.
    'plugininfo_repository' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 1,
    ),

    // Cache used by the {@link plugininfo_portfolio} class.
    'plugininfo_portfolio' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => true,
        'staticacceleration' => true,
        'staticaccelerationsize' => 1,
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
);
