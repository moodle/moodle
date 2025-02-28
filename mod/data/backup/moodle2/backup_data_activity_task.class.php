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
 * Defines backup_data_activity_task
 *
 * @package     mod_data
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/data/backup/moodle2/backup_data_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the Database instance
 */
class backup_data_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the data.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_data_activity_structure_step('data_structure', 'data.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");
        $baseunquoted = $CFG->wwwroot;

        // Link to the list of datas.
        $search = '/(' . $base . '\/mod\/data\/index.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@DATAINDEX*$2@$', $content);

        // Link to the list of datas, urlencoded.
        $search = '/(' . urlencode($baseunquoted . '/mod/data/index.php?id=') . ')([0-9]+)/';
        $content = preg_replace($search, '$@DATAINDEXURLENCODED*$2@$', $content);

        // Link to data view by moduleid.
        $search = '/(' . $base . '\/mod\/data\/view.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@DATAVIEWBYID*$2@$', $content);

        // Link to data view by moduleid, urlencoded.
        $search = '/(' . urlencode($baseunquoted . '/mod/data/view.php?id=') . ')([0-9]+)/';
        $content = preg_replace($search, '$@DATAVIEWBYIDURLENCODED*$2@$', $content);

        // Link to one "record" of the database.
        $search = '/(' . $base . '\/mod\/data\/view.php\?d\=)([0-9]+)\&(amp;)rid\=([0-9]+)/';
        $content = preg_replace($search, '$@DATAVIEWRECORD*$2*$4@$', $content);

        // Link to one "record" of the database, urlencoded.
        $search = '/(' . urlencode($baseunquoted . '/mod/data/view.php?d=') . ')([0-9]+)%26rid%3D([0-9]+)/';
        $content = preg_replace($search, '$@DATAVIEWRECORDURLENCODED*$2*$3@$', $content);

        // Link to database view by databaseid.
        $search = '/(' . $base . '\/mod\/data\/view.php\?d\=)([0-9]+)/';
        $content = preg_replace($search, '$@DATAVIEWBYD*$2@$', $content);

        // Link to database view by databaseid, urlencoded.
        $search = '/(' . urlencode($baseunquoted . '/mod/data/view.php?d=') . ')([0-9]+)/';
        $content = preg_replace($search, '$@DATAVIEWBYDURLENCODED*$2@$', $content);

        // Link to the edit page.
        $search = '/(' . $base . '\/mod\/data\/edit.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@DATAEDITBYID*$2@$', $content);

        // Link to the edit page, urlencoded.
        $search = '/(' . urlencode($baseunquoted . '/mod/data/edit.php?id=') . ')([0-9]+)/';
        $content = preg_replace($search, '$@DATAEDITBYIDURLENCODED*$2@$', $content);

        // Link to the edit page by databaseid.
        $search = '/(' . $base . '\/mod\/data\/edit.php\?d\=)([0-9]+)/';
        $content = preg_replace($search, '$@DATAEDITBYD*$2@$', $content);

        // Link to the edit page by databaseid, urlencoded.
        $search = '/(' . urlencode($baseunquoted . '/mod/data/edit.php?d=') . ')([0-9]+)/';
        $content = preg_replace($search, '$@DATAEDITBYDURLENCODED*$2@$', $content);

        return $content;
    }
}
