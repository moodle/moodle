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
 * Privacy Subsystem implementation for mod_choice.
 *
 * @package    mod_choice
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_choice\privacy;

use context_module;
use core_privacy\metadata\item_collection;
use core_privacy\metadata\provider as core_provider;
use core_privacy\request\approved_contextlist;
use core_privacy\request\contextlist;
use core_privacy\request\deletion_criteria;
use core_privacy\request\plugin\provider as plugin_provider;
use core_privacy\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the choice activity module.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements core_provider, plugin_provider {
    /**
     * @inheritdoc
     */
    public static function get_metadata(item_collection $items) : item_collection {
        $items->add_database_table(
            'choice_answers',
            [
                'choiceid' => 'privacy:metadata:choice_answers:choiceid',
                'optionid' => 'privacy:metadata:choice_answers:optionid',
                'userid' => 'privacy:metadata:choice_answers:userid',
                'timemodified' => 'privacy:metadata:choice_answers:timemodified',
            ],
            'privacy:metadata:choice_answers'
        );

        return $items;
    }

    /**
     * @inheritdoc
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        // Fetch all choice answers.
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {choice} ch ON ch.id = cm.instance
             LEFT JOIN {choice_options} co ON co.choiceid = ch.id
             LEFT JOIN {choice_answers} ca ON ca.optionid = co.id AND ca.choiceid = ch.id
                 WHERE ca.userid = :userid";

        $params = [
            'modname'       => 'choice',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * @inheritdoc
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT ca.id,
                       cm.id AS cmid,
                       ch.id  as choiceid,
                       ch.name as choicename,
                       ch.intro,
                       ch.introformat,
                       co.text as answer,                      
                       ca.timemodified
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid
            INNER JOIN {choice} ch ON ch.id = cm.instance
             LEFT JOIN {choice_options} co ON co.choiceid = ch.id
             LEFT JOIN {choice_answers} ca ON ca.optionid = co.id AND ca.choiceid = ch.id
                 WHERE c.id {$contextsql}
                       AND ca.userid = :userid";

        $params = [
            'userid'  => $userid
        ];
        $params += $contextparams;

        $choiceanswers = $DB->get_recordset_sql($sql, $params);
        // Group choice answers per activity. (We might fetch multiple choice answers that belong to a single choice activity).
        $answergroups = [];
        foreach ($choiceanswers as $choiceanswer) {
            $cmid = $choiceanswer->cmid;
            if (empty($answergroups[$cmid])) {
                $context = context_module::instance($cmid);
                $data = (object)[
                    'id' => $choiceanswer->id,
                    'choiceid' => $choiceanswer->choiceid,
                    'choicename' => $choiceanswer->choicename,
                    'answer' => $choiceanswer->answer,
                    'timemodified' => $choiceanswer->timemodified,
                ];

                $data->intro = writer::with_context($context)
                    ->rewrite_pluginfile_urls([], 'mod_choice', 'intro', $choiceanswer->choiceid, $choiceanswer->intro);
                $data->answer = [$choiceanswer->answer];
                $answergroups[$choiceanswer->cmid] = $data;
            } else {
                $answergroups[$choiceanswer->cmid]->answer[] = $choiceanswer->answer;
            }
        }
        $choiceanswers->close();

        // Export the data.
        foreach ($answergroups as $cmid => $answergroup) {
            $context = context_module::instance($cmid);
            writer::with_context($context)
                // Export the choice answer.
                ->export_data([], $answergroup)

                // Export the associated files.
                ->export_area_files([], 'mod_choice', 'intro', $answergroup->choiceid);
        }
    }

    /**
     * @inheritdoc
     */
    public static function delete_for_context(deletion_criteria $criteria) {
        global $DB;

        $context = $criteria->get_context();
        if (empty($context)) {
            return;
        }
        $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
        $DB->delete_records('choice_answers', ['choiceid' => $instanceid]);
    }

    /**
     * @inheritdoc
     */
    public static function delete_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            $instanceid = $DB->get_field('course_modules', 'instance', ['id' => $context->instanceid], MUST_EXIST);
            $DB->delete_records('choice_answers', ['choiceid' => $instanceid, 'userid' => $userid]);
        }
    }
}
