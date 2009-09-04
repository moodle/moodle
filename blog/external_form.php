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
 * Moodleform for the user interface for managing external blog links.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');

class blog_edit_external_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('text', 'name', get_string('name'));
        // No need to require the name, it gets prefilled with the external blog's site name if empty
        // $mform->addRule('name', get_string('emptyname', 'blog'), 'required', null, 'client');
        $mform->setHelpButton('name', array('name', get_string('name', 'blog'), 'blog'));

        $mform->addElement('textarea', 'description', get_string('description'), array('cols' => 50, 'rows' => 7));
        $mform->setHelpButton('description', array('description', get_string('description', 'blog'), 'blog'));

        $mform->addElement('text', 'url', get_string('url'));
        $mform->addRule('url', get_string('emptyurl', 'blog'), 'required', null, 'client');
        $mform->setHelpButton('url', array('url', get_string('url', 'blog'), 'blog'));

        if (!empty($CFG->usetags)) {
            $mform->addElement('text', 'tags', get_string('tags'));
            $mform->setHelpButton('tags', array('tags', get_string('tags', 'blog'), 'blog'));
        }

        $this->add_action_buttons();

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_URL);
        $mform->setDefault('returnurl', 0);
    }

    /**
     * Additional validation includes checking URL and tags
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!blog_is_valid_url($data['url'])) {
            $errors['url'] = get_string('invalidurl', 'blog');
        } else {
            $rss = fetch_rss($data['url']);
            if (empty($rss->channel)) {
                $errors['url'] = get_string('emptyrssfeed', 'blog');
            }
        }

        return $errors;
    }

/// tweak the form - depending on existing data
    public function definition_after_data() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        $name = trim($mform->getElementValue('name'));
        $description = trim($mform->getElementValue('description'));
        $url = $mform->getElementValue('url');

        if (empty($name) || empty($description)) {
            $rss = fetch_rss($url);

            if (empty($name) && !empty($rss->channel['title'])) {
                $mform->setDefault('name', $rss->channel['title']);
            }

            if (empty($description) && !empty($rss->channel['description'])) {
                $mform->setDefault('description', $rss->channel['description']);
            }
        }

        if ($id = $mform->getElementValue('id')) {
            $mform->setDefault('tags', implode(',', tag_get_tags_array('blog_external', $id)));
        }
    }
}
