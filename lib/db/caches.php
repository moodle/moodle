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
        'persistent' => true,
        'persistentmaxsize' => 30
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
        'persistent' => true,
        'persistentmaxsize' => 15
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
        'persistent' => true,
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
    )
);
