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
 * Form for editing tag block instances.
 *
 * @package   block_tags
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing tag block instances.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_tags_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG;
        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_tags'));
        $mform->setType('config_title', PARAM_TEXT);
        $mform->setDefault('config_title', get_string('pluginname', 'block_tags'));

        $this->add_collection_selector($mform);

        $numberoftags = array();
        for ($i = 1; $i <= 200; $i++) {
            $numberoftags[$i] = $i;
        }
        $mform->addElement('select', 'config_numberoftags', get_string('numberoftags', 'blog'), $numberoftags);
        $mform->setDefault('config_numberoftags', 80);

        $defaults = array(
            core_tag_tag::STANDARD_ONLY => get_string('standardonly', 'block_tags'),
            core_tag_tag::BOTH_STANDARD_AND_NOT => get_string('anytype', 'block_tags'));
        $mform->addElement('select', 'config_showstandard', get_string('defaultdisplay', 'block_tags'), $defaults);
        $mform->setDefault('config_showstandard', core_tag_tag::BOTH_STANDARD_AND_NOT);

        $defaults = array(0 => context_system::instance()->get_context_name());
        $parentcontext = context::instance_by_id($this->block->instance->parentcontextid);
        if ($parentcontext->contextlevel > CONTEXT_COURSE) {
            $coursecontext = $parentcontext->get_course_context();
            $defaults[$coursecontext->id] = $coursecontext->get_context_name();
        }
        if ($parentcontext->contextlevel != CONTEXT_SYSTEM) {
            $defaults[$parentcontext->id] = $parentcontext->get_context_name();
        }
        $mform->addElement('select', 'config_ctx', get_string('taggeditemscontext', 'block_tags'), $defaults);
        $mform->addHelpButton('config_ctx', 'taggeditemscontext', 'block_tags');
        $mform->setDefault('config_ctx', 0);

        $mform->addElement('advcheckbox', 'config_rec', get_string('recursivecontext', 'block_tags'));
        $mform->addHelpButton('config_rec', 'recursivecontext', 'block_tags');
        $mform->setDefault('config_rec', 1);
    }

    /**
     * Add the tag collection selector
     *
     * @param object $mform the form being built.
     */
    protected function add_collection_selector($mform) {
        $tagcolls = core_tag_collection::get_collections_menu(false, true, get_string('anycollection', 'block_tags'));
        if (count($tagcolls) <= 1) {
            $mform->addElement('hidden', 'config_tagcoll', 0);
            $mform->setType('config_tagcoll', PARAM_INT);
            return;
        }

        $mform->addElement('select', 'config_tagcoll', get_string('tagcollection', 'block_tags'), $tagcolls);
        $mform->setDefault('config_tagcoll', 0);
    }
}
