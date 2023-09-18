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
 * Steps definitions related to mod_quiz.
 *
 * @package   mod_wiki
 * @category  test
 * @copyright 2023 Catalyst IT Europe Ltd.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions related to mod_wiki.
 *
 * @copyright 2023 Catalyst IT Europe Ltd.
 * @author Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_wiki extends behat_base {

    /**
     * Add the specified pages to the specified wiki
     *
     * The first row should be column names:
     * | wiki | user | group | title | content |
     *
     *
     * wiki        idnumber of the wiki course module
     * user        username of the user who is creating the page
     * group       (optional) idnumber of the group the page belongs to
     * title       (optional) the title text of the page
     * content     (optional) the content of the page
     *
     * @param TableNode $data The pages to add
     *
     * @Given /^the following wiki pages exist:$/
     */
    public function the_following_wiki_pages_exist(TableNode $data): void {
        global $DB;

        $generator = behat_util::get_data_generator()->get_plugin_generator('mod_wiki');
        // Add the pages.
        foreach ($data->getHash() as $pagedata) {
            if (!array_key_exists('wiki', $pagedata)) {
                throw new ExpectationException('When adding pages to a wiki, ' .
                        'the wiki column is required containing the wiki idnumber.', $this->getSession());
            }

            $wikicm = $this->get_course_module_for_identifier($pagedata['wiki']);
            $wiki = $DB->get_record('wiki', ['id' => $wikicm->instance]);
            $wiki->cmid = $wikicm->cmid;
            $pagedata['wikiid'] = $wiki->id;
            unset($pagedata['wiki']);

            if (array_key_exists('group', $pagedata)) {
                $pagedata['group'] = $DB->get_field('groups', 'id', ['idnumber' => $pagedata['group']], MUST_EXIST);
            }

            if (array_key_exists('user', $pagedata)) {
                $pagedata['userid'] = $DB->get_field('user', 'id', ['username' => $pagedata['user']], MUST_EXIST);
                unset($pagedata['user']);
            }

            $generator->create_page($wiki, $pagedata);
        }
    }
}
