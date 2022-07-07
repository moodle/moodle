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
 * @package   core_form
 * @category  test
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../question/tests/behat/behat_question_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions related to core_form.
 */
class behat_core_form extends behat_question_base {

    /**
     * Convert page names to URLs for steps like 'When I am on the "core_form > [page name]" page'.
     *
     * Recognised page names are:
     * | None so far!      |                                                              |
     *
     * @param string $page name of the page, with the component name removed e.g. 'Admin notification'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_url(string $page): moodle_url {
        switch ($page) {
            default:
                throw new Exception('Unrecognised core_form page type "' . $page . '."');
        }
    }

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "core_form > [page type]" page'.
     *
     * Recognised page names are:
     * | pagetype | name meaning | description                         |
     * | Fixture  | script-name  | Fixture file name without extension |
     *
     * The fixture name should be the filename without path or extension. E.g.
     * autocomplete-disabledif for lib/form/tests/fixtures/autocomplete-disabledif.php.
     *
     * @param string $type identifies which type of page this is, e.g. 'Fixture'.
     * @param string $identifier identifies the particular page, e.g. 'autocomplete-disabledif'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        switch ($type) {
            case 'Fixture':
                return new moodle_url('/lib/form/tests/fixtures/' .
                        clean_param($identifier, PARAM_ALPHAEXT) . '.php');

            default:
                throw new Exception('Unrecognised core_form page type "' . $type . '."');
        }
    }
}
