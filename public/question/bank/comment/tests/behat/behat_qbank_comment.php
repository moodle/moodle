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
 * Commenting system steps definitions for question.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../tests/behat/behat_question_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Steps definitions to deal with the commenting system in question.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qbank_comment extends behat_question_base {

    /**
     * Adds the specified option to the question comments of the current modal.
     *
     * @Then I add :arg1 comment to question
     * @param string $comment
     */
    public function i_add_comment_to_question($comment) {

        // Getting the textarea and setting the provided value.
        $exception = new ElementNotFoundException($this->getSession(), 'Question ');

        if ($this->running_javascript()) {
            $commentstextarea = $this->find('css',
                                        '.modal-dialog .question-comment-view .comment-area textarea', $exception);
            $commentstextarea->setValue($comment);

            // We delay 1 second which is all we need.
            $this->getSession()->wait(1000);

        } else {
            throw new ExpectationException('JavaScript not running', $this->getSession());
        }
    }

    /**
     * Adds the specified option to the question comments of the question preview.
     *
     * @Then I add :arg1 comment to question preview
     * @param string $comment
     */
    public function i_add_comment_to_question_preview($comment) {

        // Getting the textarea and setting the provided value.
        $exception = new ElementNotFoundException($this->getSession(), 'Question ');

        if ($this->running_javascript()) {
            $commentstextarea = $this->find('css',
                    '.comment-area textarea', $exception);
            $commentstextarea->setValue($comment);

            // We delay 1 second which is all we need.
            $this->getSession()->wait(1000);

        } else {
            throw new ExpectationException('JavaScript not running', $this->getSession());
        }
    }

    /**
     * Deletes the specified comment from the current question comment preview.
     *
     * @Then I delete :arg comment from question preview
     * @param string $comment
     */
    public function i_delete_comment_from_question_preview($comment) {

        $exception = new ElementNotFoundException($this->getSession(), '"' . $comment . '" comment ');

        // Using xpath liternal to avoid possible problems with comments containing quotes.
        $commentliteral = behat_context_helper::escape($comment);

        $commentxpath = "//*[contains(concat(' ', normalize-space(@class), ' '), ' comment-ctrl ')]" .
            "/descendant::div[@class='comment-message'][contains(., $commentliteral)]";

        // Click on delete icon.
        $this->execute('behat_general::i_click_on_in_the',
            ["Delete comment posted by", "icon", $this->escape($commentxpath), "xpath_element"]
        );

        // Wait for the animation to finish, in theory is just 1 sec, adding 4 just in case.
        $this->getSession()->wait(4 * 1000);
    }

    /**
     * Deletes the specified comment from the current question comment modal.
     *
     * @Then I delete :arg comment from question
     * @param string $comment
     */
    public function i_delete_comment_from_question($comment) {

        $exception = new ElementNotFoundException($this->getSession(), '"' . $comment . '" comment ');

        // Using xpath liternal to avoid possible problems with comments containing quotes.
        $commentliteral = behat_context_helper::escape($comment);

        $commentxpath = "//*[contains(concat(' ', normalize-space(@class), ' '), ' question-comment-view ')]" .
                "/descendant::div[@class='comment-message'][contains(., $commentliteral)]";

        // Click on delete icon.
        $this->execute('behat_general::i_click_on_in_the',
                ["Delete comment posted by", "icon", $this->escape($commentxpath), "xpath_element"]
        );

        // Wait for the animation to finish, in theory is just 1 sec, adding 4 just in case.
        $this->getSession()->wait(4 * 1000);
    }

    /**
     * Define named selectors for the comments column.
     *
     * Supported selectors are:
     * - "qbank_comment > Comment count link" a comment count displayed as a link.
     * - "qbank_comment > Comment count text" a comment count displayed as un-linked text.
     *
     * @return behat_component_named_selector[]
     */
    public static function get_exact_named_selectors(): array {
        $commentcountxpath = "//table/tbody/tr/td[contains(@class, 'commentcount')]/%s[text() = %%locator%%]";
        return [
            new behat_component_named_selector(
                'Comment count link',
                [sprintf($commentcountxpath, 'a')]
            ),
            new behat_component_named_selector(
                'Comment count text',
                [sprintf($commentcountxpath, 'span')]
            ),
        ];
    }
}
