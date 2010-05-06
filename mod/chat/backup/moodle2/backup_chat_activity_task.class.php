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
 * This is the "graphical" structure of the chat mod:
 *
 *                        chat
 *                     (CL,pk->id)
 *                         |
 *                         |
 *                         |
 *                    chat_messages
 *                (UL,pk->id, fk->chatid)
 *
 *  Meaning: pk->primary key field of the table
 *           fk->foreign key to link with parent
 *           nt->nested field (recursive data)
 *           CL->course level info
 *           UL->user level info
 *
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/mod/chat/backup/moodle2/backup_chat_stepslib.php'); // Because it exists (must)

/**
 * chat backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_chat_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // chat only has one structure step
        $this->add_step(new backup_chat_activity_structure_step('chat_structure', 'chat.xml'));
    }

    /**
     * Code the transformations to perform in the chat activity in
     * order to get transportable (encoded) links
     *
     * @param string $content
     * @return string
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot.'/mod/chat','#');

        //Link to the list of chats
        $pattern = "#(".$base."\/index.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@CHATINDEX*$2@$', $content);

        //Link to chat view by moduleid
        $pattern = "#(".$base."\/view.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@CHATVIEWBYID*$2@$', $content);

        return $content;
    }
}
