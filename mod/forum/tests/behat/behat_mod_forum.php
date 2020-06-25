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
 * Steps definitions related with the forum activity.
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
/**
 * Forum-related steps definitions.
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_forum extends behat_base {

    /**
     * Adds a topic to the forum specified by it's name. Useful for the Announcements and blog-style forums.
     *
     * @Given /^I add a new topic to "(?P<forum_name_string>(?:[^"]|\\")*)" forum with:$/
     * @param string $forumname
     * @param TableNode $table
     */
    public function i_add_a_new_topic_to_forum_with($forumname, TableNode $table) {
        $this->add_new_discussion($forumname, $table, get_string('addanewtopic', 'forum'));
    }

    /**
     * Adds a Q&A discussion to the Q&A-type forum specified by it's name with the provided table data.
     *
     * @Given /^I add a new question to "(?P<forum_name_string>(?:[^"]|\\")*)" forum with:$/
     * @param string $forumname
     * @param TableNode $table
     */
    public function i_add_a_new_question_to_forum_with($forumname, TableNode $table) {
        $this->add_new_discussion($forumname, $table, get_string('addanewquestion', 'forum'));
    }

    /**
     * Adds a discussion to the forum specified by it's name with the provided table data (usually Subject and Message). The step begins from the forum's course page.
     *
     * @Given /^I add a new discussion to "(?P<forum_name_string>(?:[^"]|\\")*)" forum with:$/
     * @param string $forumname
     * @param TableNode $table
     */
    public function i_add_a_forum_discussion_to_forum_with($forumname, TableNode $table) {
        $this->add_new_discussion($forumname, $table, get_string('addanewdiscussion', 'forum'));
    }

    /**
     * Adds a discussion to the forum specified by it's name with the provided table data (usually Subject and Message).
     * The step begins from the forum's course page.
     *
     * @Given /^I add a new discussion to "(?P<forum_name_string>(?:[^"]|\\")*)" forum inline with:$/
     * @param string $forumname
     * @param TableNode $table
     */
    public function i_add_a_forum_discussion_to_forum_inline_with($forumname, TableNode $table) {
        $this->add_new_discussion_inline($forumname, $table, get_string('addanewdiscussion', 'forum'));
    }

    /**
     * Adds a reply to the specified post of the specified forum. The step begins from the forum's page or from the forum's course page.
     *
     * @Given /^I reply "(?P<post_subject_string>(?:[^"]|\\")*)" post from "(?P<forum_name_string>(?:[^"]|\\")*)" forum with:$/
     * @param string $postname The subject of the post
     * @param string $forumname The forum name
     * @param TableNode $table
     */
    public function i_reply_post_from_forum_with($postsubject, $forumname, TableNode $table) {

        // Navigate to forum.
        $this->goto_main_post_reply($postsubject);

        // Fill form and post.
        $this->execute('behat_forms::i_set_the_following_fields_to_these_values', $table);

        $this->execute('behat_forms::press_button', get_string('posttoforum', 'forum'));
        $this->execute('behat_general::i_wait_to_be_redirected');
    }

    /**
     * Inpage Reply - adds a reply to the specified post of the specified forum. The step begins from the forum's page or from the forum's course page.
     *
     * @Given /^I reply "(?P<post_subject_string>(?:[^"]|\\")*)" post from "(?P<forum_name_string>(?:[^"]|\\")*)" forum using an inpage reply with:$/
     * @param string $postsubject The subject of the post
     * @param string $forumname The forum name
     * @param TableNode $table
     */
    public function i_reply_post_from_forum_using_an_inpage_reply_with($postsubject, $forumname, TableNode $table) {

        // Navigate to forum.
        $this->execute('behat_general::click_link', $this->escape($forumname));
        $this->execute('behat_general::click_link', $this->escape($postsubject));
        $this->execute('behat_general::click_link', get_string('reply', 'forum'));

        // Fill form and post.
        $this->execute('behat_forms::i_set_the_following_fields_to_these_values', $table);

        $this->execute('behat_forms::press_button', get_string('submit', 'core'));
    }

    /**
     * Navigates to a particular discussion page
     *
     * @Given /^I navigate to post "(?P<post_subject_string>(?:[^"]|\\")*)" in "(?P<forum_name_string>(?:[^"]|\\")*)" forum$/
     * @param string $postsubject The subject of the post
     * @param string $forumname The forum name
     */
    public function i_navigate_to_post_in_forum($postsubject, $forumname) {

        // Navigate to forum discussion.
        $this->execute('behat_general::click_link', $this->escape($forumname));
        $this->execute('behat_general::click_link', $this->escape($postsubject));
    }

    /**
     * Opens up the action menu for the discussion
     *
     * @Given /^I click on "(?P<post_subject_string>(?:[^"]|\\")*)" action menu$/
     * @param string $discussion The subject of the discussion
     */
    public function i_click_on_action_menu($discussion) {
        $this->execute('behat_general::i_click_on_in_the', [
            "[data-container='discussion-tools'] [data-toggle='dropdown']", "css_element",
            "//tr[contains(concat(' ', normalize-space(@class), ' '), ' discussion ') and contains(.,'$discussion')]",
            "xpath_element"
        ]);
    }

    /**
     * Returns the steps list to add a new discussion to a forum.
     *
     * Abstracts add a new topic and add a new discussion, as depending
     * on the forum type the button string changes.
     *
     * @param string $forumname
     * @param TableNode $table
     * @param string $buttonstr
     */
    protected function add_new_discussion($forumname, TableNode $table, $buttonstr) {

        // Navigate to forum.
        $this->execute('behat_general::click_link', $this->escape($forumname));
        $this->execute('behat_general::click_link', $buttonstr);
        $this->execute('behat_forms::press_button', get_string('showadvancededitor'));

        $this->fill_new_discussion_form($table);
    }

    /**
     * Returns the steps list to add a new discussion to a forum inline.
     *
     * Abstracts add a new topic and add a new discussion, as depending
     * on the forum type the button string changes.
     *
     * @param string $forumname
     * @param TableNode $table
     * @param string $buttonstr
     */
    protected function add_new_discussion_inline($forumname, TableNode $table, $buttonstr) {

        // Navigate to forum.
        $this->execute('behat_general::click_link', $this->escape($forumname));
        $this->execute('behat_general::click_link', $buttonstr);
        $this->fill_new_discussion_form($table);
    }

    /**
     * Fill in the forum's post form and submit. It assumes you've already navigated and enabled the form for view.
     *
     * @param TableNode $table
     * @throws coding_exception
     */
    protected function fill_new_discussion_form(TableNode $table) {
        // Fill form and post.
        $this->execute('behat_forms::i_set_the_following_fields_to_these_values', $table);
        $this->execute('behat_forms::press_button', get_string('posttoforum', 'forum'));
        $this->execute('behat_general::i_wait_to_be_redirected');
    }

    /**
     * Go to the default reply to post page.
     * This is used instead of navigating through 4-5 different steps and to solve issues where JS would be required to click
     * on the advanced button
     *
     * @param $postsubject
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    protected function goto_main_post_reply($postsubject) {
        global $DB;
        $post = $DB->get_record("forum_posts", array("subject" => $postsubject), 'id', MUST_EXIST);
        $url = new moodle_url('/mod/forum/post.php', ['reply' => $post->id]);
        $this->execute('behat_general::i_visit', [$url]);
    }
}
