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
 * Commenting system steps definitions.
 *
 * @package    block_comments
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions to deal with the commenting system
 *
 * @package    block_comments
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_comments extends behat_base {

    /**
     * Adds the specified option to the comments block of the current page.
     *
     * This method can be adapted in future to add other comments considering
     * that there could be more than one comment textarea per page.
     *
     * Only 1 comments block instance is allowed per page, if this changes this
     * steps definitions should be adapted.
     *
     * @Given /^I add "(?P<comment_text_string>(?:[^"]|\\")*)" comment to comments block$/
     * @throws ElementNotFoundException
     * @param string $comment
     */
    public function i_add_comment_to_comments_block($comment) {

        // Getting the textarea and setting the provided value.
        $exception = new ElementNotFoundException($this->getSession(), 'Comments block ');

        // The whole DOM structure changes depending on JS enabled/disabled.
        if ($this->running_javascript()) {
            $commentstextarea = $this->find('css', '.comment-area textarea', $exception);
            $commentstextarea->setValue($comment);

            $this->find_link(get_string('savecomment'))->click();
            // Delay after clicking so that additional comments will have unique time stamps.
            // We delay 1 second which is all we need.
            $this->getSession()->wait(1000, false);

        } else {

            $commentstextarea = $this->find('css', '.block_comments form textarea', $exception);
            $commentstextarea->setValue($comment);

            // Comments submit button
            $submit = $this->find('css', '.block_comments form input[type=submit]');
            $submit->press();
        }
    }

    /**
     * Deletes the specified comment from the current page's comments block.
     *
     * @Given /^I delete "(?P<comment_text_string>(?:[^"]|\\")*)" comment from comments block$/
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @param string $comment
     */
    public function i_delete_comment_from_comments_block($comment) {

        $exception = new ElementNotFoundException($this->getSession(), '"' . $comment . '" comment ');

        // Using xpath liternal to avoid possible problems with comments containing quotes.
        $commentliteral = behat_context_helper::escape($comment);

        $commentxpath = "//*[contains(concat(' ', normalize-space(@class), ' '), ' block_comments ')]" .
            "/descendant::div[@class='comment-message'][contains(., $commentliteral)]";
        $commentnode = $this->find('xpath', $commentxpath, $exception);

        // Click on delete icon.
        $this->execute('behat_general::i_click_on_in_the',
            array("Delete comment posted by", "icon", $this->escape($commentxpath), "xpath_element")
        );

        // Wait for the animation to finish, in theory is just 1 sec, adding 4 just in case.
        $this->getSession()->wait(4 * 1000, false);
    }

}
