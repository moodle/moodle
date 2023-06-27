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
 * A scheduled task.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

use core_tag_collection, core_tag_tag, core_tag_area, stdClass;

/**
 * Simple task to run the tag cron.
 */
class tag_cron_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasktagcron', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;

        if (!empty($CFG->usetags)) {
            $this->compute_correlations();
            $this->cleanup();
        }
    }

    /**
     * Calculates and stores the correlated tags of all tags.
     *
     * The correlations are stored in the 'tag_correlation' table.
     *
     * Two tags are correlated if they appear together a lot. Ex.: Users tagged with "computers"
     * will probably also be tagged with "algorithms".
     *
     * The rationale for the 'tag_correlation' table is performance. It works as a cache
     * for a potentially heavy load query done at the 'tag_instance' table. So, the
     * 'tag_correlation' table stores redundant information derived from the 'tag_instance' table.
     *
     * @param int $mincorrelation Only tags with more than $mincorrelation correlations will be identified.
     */
    public function compute_correlations($mincorrelation = 2) {
        global $DB;

        // This mighty one line query fetches a row from the database for every
        // individual tag correlation. We then need to process the rows collecting
        // the correlations for each tag id.
        // The fields used by this query are as follows:
        //   tagid         : This is the tag id, there should be at least $mincorrelation
        //                   rows for each tag id.
        //   correlation   : This is the tag id that correlates to the above tagid field.
        //   correlationid : This is the id of the row in the tag_correlation table that
        //                   relates to the tagid field and will be NULL if there are no
        //                   existing correlations.
        $sql = 'SELECT pairs.tagid, pairs.correlation, pairs.ocurrences, co.id AS correlationid
                  FROM (
                           SELECT ta.tagid, tb.tagid AS correlation, COUNT(*) AS ocurrences
                             FROM {tag_instance} ta
                             JOIN {tag} tga ON ta.tagid = tga.id
                             JOIN {tag_instance} tb ON (ta.itemtype = tb.itemtype AND ta.component = tb.component
                                AND ta.itemid = tb.itemid AND ta.tagid <> tb.tagid)
                             JOIN {tag} tgb ON tb.tagid = tgb.id AND tgb.tagcollid = tga.tagcollid
                         GROUP BY ta.tagid, tb.tagid
                           HAVING COUNT(*) > :mincorrelation
                       ) pairs
             LEFT JOIN {tag_correlation} co ON co.tagid = pairs.tagid
              ORDER BY pairs.tagid ASC, pairs.ocurrences DESC, pairs.correlation ASC';
        $rs = $DB->get_recordset_sql($sql, array('mincorrelation' => $mincorrelation));

        // Set up an empty tag correlation object.
        $tagcorrelation = new stdClass;
        $tagcorrelation->id = null;
        $tagcorrelation->tagid = null;
        $tagcorrelation->correlatedtags = array();

        // We store each correlation id in this array so we can remove any correlations
        // that no longer exist.
        $correlations = array();

        // Iterate each row of the result set and build them into tag correlations.
        // We add all of a tag's correlations to $tagcorrelation->correlatedtags[]
        // then save the $tagcorrelation object.
        foreach ($rs as $row) {
            if ($row->tagid != $tagcorrelation->tagid) {
                // The tag id has changed so we have all of the correlations for this tag.
                $tagcorrelationid = $this->process_computed_correlation($tagcorrelation);
                if ($tagcorrelationid) {
                    $correlations[] = $tagcorrelationid;
                }
                // Now we reset the tag correlation object so we can reuse it and set it
                // up for the current record.
                $tagcorrelation = new stdClass;
                $tagcorrelation->id = $row->correlationid;
                $tagcorrelation->tagid = $row->tagid;
                $tagcorrelation->correlatedtags = array();
            }
            // Save the correlation on the tag correlation object.
            $tagcorrelation->correlatedtags[] = $row->correlation;
        }
        // Update the current correlation after the last record.
        $tagcorrelationid = $this->process_computed_correlation($tagcorrelation);
        if ($tagcorrelationid) {
            $correlations[] = $tagcorrelationid;
        }

        // Close the recordset.
        $rs->close();

        // Remove any correlations that weren't just identified.
        if (empty($correlations)) {
            // There are no tag correlations.
            $DB->delete_records('tag_correlation');
        } else {
            list($sql, $params) = $DB->get_in_or_equal($correlations,
                    SQL_PARAMS_NAMED, 'param0000', false);
            $DB->delete_records_select('tag_correlation', 'id '.$sql, $params);
        }
    }

    /**
     * Clean up the tag tables, making sure all tagged object still exists.
     *
     * This method is called from cron.
     *
     * This should normally not be necessary, but in case related tags are not deleted
     * when the tagged record is removed, this should be done once in a while, perhaps
     * on an occasional cron run.  On a site with lots of tags, this could become an
     * expensive function to call.
     */
    public function cleanup() {
        global $DB;

        // Get ids to delete from instances where the tag has been deleted. This should never happen apparently.
        $sql = "SELECT ti.id
                  FROM {tag_instance} ti
             LEFT JOIN {tag} t ON t.id = ti.tagid
                 WHERE t.id IS null";
        $tagids = $DB->get_records_sql($sql);
        $tagarray = array();
        foreach ($tagids as $tagid) {
            $tagarray[] = $tagid->id;
        }

        // Next get ids from instances that have an owner that has been deleted.
        $sql = "SELECT ti.id
                  FROM {tag_instance} ti, {user} u
                 WHERE ti.itemid = u.id
                   AND ti.itemtype = 'user'
                   AND ti.component = 'core'
                   AND u.deleted = 1";
        $tagids = $DB->get_records_sql($sql);
        foreach ($tagids as $tagid) {
            $tagarray[] = $tagid->id;
        }

        // Get the other itemtypes.
        $sql = "SELECT DISTINCT component, itemtype
                  FROM {tag_instance}
                 WHERE itemtype <> 'user' or component <> 'core'";
        $tagareas = $DB->get_recordset_sql($sql);
        foreach ($tagareas as $tagarea) {
            $sql = 'SELECT ti.id
                      FROM {tag_instance} ti
                 LEFT JOIN {' . $tagarea->itemtype . '} it ON it.id = ti.itemid
                     WHERE it.id IS null
                     AND ti.itemtype = ? AND ti.component = ?';
            $tagids = $DB->get_records_sql($sql, array($tagarea->itemtype, $tagarea->component));
            foreach ($tagids as $tagid) {
                $tagarray[] = $tagid->id;
            }
        }
        $tagareas->close();

        // Get instances for each of the ids to be deleted.
        if (count($tagarray) > 0) {
            list($sqlin, $params) = $DB->get_in_or_equal($tagarray);
            $sql = "SELECT ti.*, COALESCE(t.name, 'deleted') AS name, COALESCE(t.rawname, 'deleted') AS rawname
                      FROM {tag_instance} ti
                 LEFT JOIN {tag} t ON t.id = ti.tagid
                     WHERE ti.id $sqlin";
            $instances = $DB->get_records_sql($sql, $params);
            $this->bulk_delete_instances($instances);
        }

        core_tag_collection::cleanup_unused_tags();
    }

    /**
     * This function processes a tag correlation and makes changes in the database as required.
     *
     * The tag correlation object needs have both a tagid property and a correlatedtags property that is an array.
     *
     * @param   stdClass $tagcorrelation
     * @return  int/bool The id of the tag correlation that was just processed or false.
     */
    public function process_computed_correlation(stdClass $tagcorrelation) {
        global $DB;

        // You must provide a tagid and correlatedtags must be set and be an array.
        if (empty($tagcorrelation->tagid) || !isset($tagcorrelation->correlatedtags) ||
                !is_array($tagcorrelation->correlatedtags)) {
            return false;
        }

        $tagcorrelation->correlatedtags = join(',', $tagcorrelation->correlatedtags);
        if (!empty($tagcorrelation->id)) {
            // The tag correlation already exists so update it.
            $DB->update_record('tag_correlation', $tagcorrelation);
        } else {
            // This is a new correlation to insert.
            $tagcorrelation->id = $DB->insert_record('tag_correlation', $tagcorrelation);
        }
        return $tagcorrelation->id;
    }

    /**
     * This function will delete numerous tag instances efficiently.
     * This removes tag instances only. It doesn't check to see if it is the last use of a tag.
     *
     * @param array $instances An array of tag instance objects with the addition of the tagname and tagrawname
     *        (used for recording a delete event).
     */
    public function bulk_delete_instances($instances) {
        global $DB;

        $instanceids = array();
        foreach ($instances as $instance) {
            $instanceids[] = $instance->id;
        }

        // This is a multi db compatible method of creating the correct sql when using the 'IN' value.
        // $insql is the sql statement, $params are the id numbers.
        list($insql, $params) = $DB->get_in_or_equal($instanceids);
        $sql = 'id ' . $insql;
        $DB->delete_records_select('tag_instance', $sql, $params);

        // Now go through and record each tag individually with the event system.
        foreach ($instances as $instance) {
            // Trigger tag removed event (i.e. The tag instance has been removed).
            \core\event\tag_removed::create_from_tag_instance($instance, $instance->name,
                    $instance->rawname, true)->trigger();
        }
    }
}
