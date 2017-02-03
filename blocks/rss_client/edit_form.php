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
 * Form for editing RSS client block instances.
 *
 * @package   block_rss_client
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing RSS client block instances.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_rss_client_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG, $DB, $USER;

        // Fields for editing block contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('selectyesno', 'config_display_description', get_string('displaydescriptionlabel', 'block_rss_client'));
        $mform->setDefault('config_display_description', 0);

        $mform->addElement('text', 'config_shownumentries', get_string('shownumentrieslabel', 'block_rss_client'), array('size' => 5));
        $mform->setType('config_shownumentries', PARAM_INT);
        $mform->addRule('config_shownumentries', null, 'numeric', null, 'client');
        if (!empty($CFG->block_rss_client_num_entries)) {
            $mform->setDefault('config_shownumentries', $CFG->block_rss_client_num_entries);
        } else {
            $mform->setDefault('config_shownumentries', 5);
        }

        $insql = '';
        $params = array('userid' => $USER->id);
        $rssconfig = unserialize(base64_decode($this->block->instance->configdata));
        if ($rssconfig && !empty($rssconfig->rssid)) {
            list($insql, $inparams) = $DB->get_in_or_equal($rssconfig->rssid, SQL_PARAMS_NAMED);
            $insql = "OR id $insql ";
            $params += $inparams;
        }

        $titlesql = "CASE WHEN {$DB->sql_isempty('block_rss_client','preferredtitle', false, false)}
                      THEN {$DB->sql_compare_text('title', 64)} ELSE preferredtitle END";

        $rssfeeds = $DB->get_records_sql_menu("
                SELECT id, $titlesql
                  FROM {block_rss_client}
                 WHERE userid = :userid OR shared = 1 $insql
                 ORDER BY $titlesql",
                $params);

        if ($rssfeeds) {
            $select = $mform->addElement('select', 'config_rssid', get_string('choosefeedlabel', 'block_rss_client'), $rssfeeds);
            $select->setMultiple(true);

        } else {
            $mform->addElement('static', 'config_rssid_no_feeds', get_string('choosefeedlabel', 'block_rss_client'),
                    get_string('nofeeds', 'block_rss_client'));
        }

        if (has_any_capability(array('block/rss_client:manageanyfeeds', 'block/rss_client:manageownfeeds'), $this->block->context)) {
            $mform->addElement('static', 'nofeedmessage', '',
                    '<a href="' . $CFG->wwwroot . '/blocks/rss_client/managefeeds.php?courseid=' . $this->page->course->id . '">' .
                    get_string('feedsaddedit', 'block_rss_client') . '</a>');
        }

        $mform->addElement('text', 'config_title', get_string('uploadlabel'));
        $mform->setType('config_title', PARAM_NOTAGS);

        $mform->addElement('selectyesno', 'config_block_rss_client_show_channel_link', get_string('clientshowchannellinklabel', 'block_rss_client'));
        $mform->setDefault('config_block_rss_client_show_channel_link', 0);

        $mform->addElement('selectyesno', 'config_block_rss_client_show_channel_image', get_string('clientshowimagelabel', 'block_rss_client'));
        $mform->setDefault('config_block_rss_client_show_channel_image', 0);
    }
}
