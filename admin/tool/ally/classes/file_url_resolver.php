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
 * Resolve the URL of a file to see it in context.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

/**
 * Resolve the URL of a file to see it in context.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_url_resolver {
    /**
     * @var \moodle_database
     */
    private $db;

    /**
     * @param \moodle_database $db
     */
    public function __construct(\moodle_database $db = null) {
        global $DB;

        $this->db = $db ?: $DB;
    }

    /**
     * Given a file, find a URL to view it in Moodle UI.
     *
     * @param \stored_file $file
     * @return \moodle_url|null
     */
    public function resolve_url(\stored_file $file) {
        $url = null;
        switch ($file->get_component()) {
            case 'forum':
            case 'mod_forum':
            case 'hsuforum':
            case 'mod_hsuforum':
                $url = $this->mod_forum_resolver($file);
                break;
            case 'question':
                $url = $this->question_resolver($file);
                break;
        }

        if ($url instanceof \moodle_url) {
            return $url;
        }

        return $this->default_resolver($file);
    }

    /**
     * Most generic way to get a file URL.
     *
     * @param \stored_file $file
     * @return \moodle_url
     */
    private function default_resolver(\stored_file $file) {
        $context = \context::instance_by_id($file->get_contextid());
        return $context->get_url();
    }

    /**
     * Resolve URL to forum posts.
     *
     * This also works for mod_hsuforum.
     *
     * @param \stored_file $file
     * @return \moodle_url|null
     */
    private function mod_forum_resolver(\stored_file $file) {
        if (!in_array($file->get_filearea(), ['attachment', 'post'])) {
            return null;
        }
        $plugin       = \core_component::normalize_component($file->get_component())[1];
        $discussionid = $this->db->get_field($plugin.'_posts', 'discussion', ['id' => $file->get_itemid()]);
        if (!$discussionid) {
            return null;
        }
        $url = new \moodle_url('/mod/'.$plugin.'/discuss.php', array('d' => $discussionid));
        $url->set_anchor('p'.$file->get_itemid());

        return $url;
    }

    /**
     * Resolve URL for questions.
     *
     * @param \stored_file $file
     * @return \moodle_url|null
     */
    private function question_resolver(\stored_file $file) {
        $params  = [];
        $context = \context::instance_by_id($file->get_contextid());
        if ($context instanceof \context_course) {
            $params['courseid'] = $context->instanceid;
        } else if ($context instanceof \context_module) {
            $params['cmid'] = $context->instanceid;
        } else {
            return null; // Not supported.
        }

        $id            = null;
        $questionareas = ['questiontext', 'generalfeedback', 'correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'];
        if (in_array($file->get_filearea(), $questionareas)) {
            $id = $file->get_itemid();
        } else if (in_array($file->get_filearea(), ['answer', 'answerfeedback'])) {
            $id = $this->db->get_field('question_answers', 'question', ['id' => $file->get_itemid()]);
        } else if ($file->get_filearea() === 'hint') {
            $id = $this->db->get_field('question_hints', 'questionid', ['id' => $file->get_itemid()]);
        }

        if (empty($id)) {
            return null;
        }
        $params['id'] = $id;

        return new \moodle_url('/question/question.php', $params);
    }
}
