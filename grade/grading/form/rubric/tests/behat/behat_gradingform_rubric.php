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
 * Steps definitions for rubrics.
 *
 * @package   gradingform_rubric
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions to help with rubrics.
 *
 * @package   gradingform_rubric
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_gradingform_rubric extends behat_base {

    /**
     * @var The number of levels added by default when a rubric is created.
     */
    const DEFAULT_RUBRIC_LEVELS = 3;

    /**
     * Defines the rubric with the provided data, following rubric's definition grid cells.
     *
     * This method fills the rubric of the rubric definition
     * form; the provided TableNode should contain one row for
     * each criterion and each cell of the row should contain:
     * # Criterion description
     * # Criterion level 1 name
     * # Criterion level 1 points
     * # Criterion level 2 name
     * # Criterion level 2 points
     * # Criterion level 3 .....
     *
     * Works with both JS and non-JS.
     *
     * @When /^I define the following rubric:$/
     * @throws ExpectationException
     * @param TableNode $rubric
     */
    public function i_define_the_following_rubric(TableNode $rubric) {

        // Being a smart method is nothing good when we talk about step definitions, in
        // this case we didn't have any other options as there are no labels no elements
        // id we can point to without having to "calculate" them.

        $steptableinfo = '| criterion description | level1 name  | level1 points | level2 name | level2 points | ...';

        $criteria = $rubric->getRows();

        $addcriterionbutton = $this->find_button(get_string('addcriterion', 'gradingform_rubric'));

        // Cleaning the current ones.
        $deletebuttons = $this->find_all('css', "input[value='" . get_string('criteriondelete', 'gradingform_rubric') . "']");
        if ($deletebuttons) {

            // We should reverse the deletebuttons because otherwise once we delete
            // the first one the DOM will change and the [X] one will not exist anymore.
            $deletebuttons = array_reverse($deletebuttons, true);
            foreach ($deletebuttons as $button) {
                $this->click_and_confirm($button);
            }
        }

        // The level number (NEWID$N) is not reset after each criterion.
        $levelnumber = 1;

        // The next criterion is created with the same number of levels than the last criterion.
        $defaultnumberoflevels = self::DEFAULT_RUBRIC_LEVELS;

        if ($criteria) {
            foreach ($criteria as $criterionit => $criterion) {
                // Unset empty levels in criterion.
                foreach ($criterion as $i => $value) {
                    if (empty($value)) {
                        unset($criterion[$i]);
                    }
                }

                // Remove empty criterion, as TableNode might contain them to make table rows equal size.
                $newcriterion = array();
                foreach ($criterion as $k => $c) {
                    if (!empty($c)) {
                        $newcriterion[$k] = $c;
                    }
                }
                $criterion = $newcriterion;

                // Checking the number of cells.
                if (count($criterion) % 2 === 0) {
                    throw new ExpectationException(
                        'The criterion levels should contain both definition and points, follow this format:' . $steptableinfo,
                        $this->getSession()
                    );
                }

                // Minimum 2 levels per criterion.
                // description + definition1 + score1 + definition2 + score2 = 5.
                if (count($criterion) < 5) {
                    throw new ExpectationException(
                        get_string('err_mintwolevels', 'gradingform_rubric'),
                        $this->getSession()
                    );

                }

                // Add new criterion.
                $addcriterionbutton->click();

                $criterionroot = 'rubric[criteria][NEWID' . ($criterionit + 1) . ']';

                // Getting the criterion description, this one is visible by default.
                $this->set_rubric_field_value($criterionroot . '[description]', array_shift($criterion), true);

                // When JS is disabled each criterion's levels name numbers starts from 0.
                if (!$this->running_javascript()) {
                    $levelnumber = 0;
                }

                // Setting the correct number of levels.
                $nlevels = count($criterion) / 2;
                if ($nlevels < $defaultnumberoflevels) {

                    // Removing levels if there are too much levels.
                    // When we add a new level the NEWID$N is increased from the last criterion.
                    $lastcriteriondefaultlevel = $defaultnumberoflevels + $levelnumber - 1;
                    $lastcriterionlevel = $nlevels + $levelnumber - 1;
                    for ($i = $lastcriteriondefaultlevel; $i > $lastcriterionlevel; $i--) {

                        // If JS is disabled seems that new levels are not added.
                        if ($this->running_javascript()) {
                            $deletelevel = $this->find_button($criterionroot . '[levels][NEWID' . $i . '][delete]');
                            $this->click_and_confirm($deletelevel);

                        } else {
                            // Only if the level exists.
                            $buttonname = $criterionroot . '[levels][NEWID' . $i . '][delete]';
                            if ($deletelevel = $this->getSession()->getPage()->findButton($buttonname)) {
                                $deletelevel->click();
                            }
                        }
                    }
                } else if ($nlevels > $defaultnumberoflevels) {
                    // Adding levels if we don't have enough.
                    $addlevel = $this->find_button($criterionroot . '[levels][addlevel]');
                    for ($i = ($defaultnumberoflevels + 1); $i <= $nlevels; $i++) {
                        $addlevel->click();
                    }
                }

                // Updating it.
                if ($nlevels > self::DEFAULT_RUBRIC_LEVELS) {
                    $defaultnumberoflevels = $nlevels;
                } else {
                    // If it is less than the default value it sets it to
                    // the default value.
                    $defaultnumberoflevels = self::DEFAULT_RUBRIC_LEVELS;
                }

                foreach ($criterion as $i => $value) {

                    $levelroot = $criterionroot . '[levels][NEWID' . $levelnumber . ']';

                    if ($i % 2 === 0) {
                        // Pairs are the definitions.
                        $fieldname = $levelroot . '[definition]';
                        $this->set_rubric_field_value($fieldname, $value);

                    } else {
                        // Odds are the points.

                        // Checking it now, we would need to remove it if we are testing the form validations...
                        if (!is_numeric($value)) {
                            throw new ExpectationException(
                                'The points cells should contain numeric values, follow this format: ' . $steptableinfo,
                                $this->getSession()
                            );
                        }

                        $fieldname = $levelroot . '[score]';
                        $this->set_rubric_field_value($fieldname, $value, true);

                        // Increase the level by one every 2 cells.
                        $levelnumber++;
                    }

                }
            }
        }
    }

    /**
     * Replaces a value from the specified criterion. You can use it when editing rubrics, to set both name or points.
     *
     * @When /^I replace "(?P<current_value_string>(?:[^"]|\\")*)" rubric level with "(?P<value_string>(?:[^"]|\\")*)" in "(?P<criterion_string>(?:[^"]|\\")*)" criterion$/
     * @throws ElementNotFoundException
     * @param string $currentvalue
     * @param string $value
     * @param string $criterionname
     */
    public function i_replace_rubric_level_with($currentvalue, $value, $criterionname) {

        $currentvalueliteral = behat_context_helper::escape($currentvalue);
        $criterionliteral = behat_context_helper::escape($criterionname);

        $criterionxpath = "//div[@id='rubric-rubric']" .
            "/descendant::td[contains(concat(' ', normalize-space(@class), ' '), ' description ')]";
        // It differs between JS on/off.
        if ($this->running_javascript()) {
            $criterionxpath .= "/descendant::span[@class='textvalue'][text()=$criterionliteral]" .
                "/ancestor::tr[contains(concat(' ', normalize-space(@class), ' '), ' criterion ')]";
        } else {
            $criterionxpath .= "/descendant::textarea[text()=$criterionliteral]" .
                "/ancestor::tr[contains(concat(' ', normalize-space(@class), ' '), ' criterion ')]";
        }

        $inputxpath = $criterionxpath .
            "/descendant::input[@type='text'][@value=$currentvalueliteral]";
        $textareaxpath = $criterionxpath .
            "/descendant::textarea[text()=$currentvalueliteral]";

        if ($this->running_javascript()) {

            $spansufix = "/ancestor::div[@class='level-wrapper']" .
                "/descendant::div[@class='definition']" .
                "/descendant::span[@class='textvalue']";

            // Expanding the level input boxes.
            $spannode = $this->find('xpath', $inputxpath . $spansufix . '|' . $textareaxpath . $spansufix);
            $spannode->click();

            $inputfield = $this->find('xpath', $inputxpath . '|' . $textareaxpath);
            $inputfield->setValue($value);

        } else {
            $fieldnode = $this->find('xpath', $inputxpath . '|' . $textareaxpath);
            $this->set_rubric_field_value($fieldnode->getAttribute('name'), $value);
        }

    }

    /**
     * Grades filling the current page rubric. Set one line per criterion and for each criterion set "| Criterion name | Points | Remark |".
     *
     * @When /^I grade by filling the rubric with:$/
     *
     * @throws ExpectationException
     * @param TableNode $rubric
     */
    public function i_grade_by_filling_the_rubric_with(TableNode $rubric) {

        $criteria = $rubric->getRowsHash();

        $stepusage = '"I grade by filling the rubric with:" step needs you to provide a table where each row is a criterion' .
            ' and each criterion has 3 different values: | Criterion name | Number of points | Remark text |';

        // First element -> name, second -> points, third -> Remark.
        foreach ($criteria as $name => $criterion) {

            // We only expect the points and the remark, as the criterion name is $name.
            if (count($criterion) !== 2) {
                throw new ExpectationException($stepusage, $this->getSession());
            }

            // Numeric value here.
            $points = $criterion[0];
            if (!is_numeric($points)) {
                throw new ExpectationException($stepusage, $this->getSession());
            }

            // Selecting a value.
            // When JS is disabled there are radio options, with JS enabled divs.
            $selectedlevelxpath = $this->get_level_xpath($points);
            if ($this->running_javascript()) {

                // Only clicking on the selected level if it was not already selected.
                $levelnode = $this->find('xpath', $selectedlevelxpath);

                // Using in_array() as there are only a few elements.
                if (!in_array('checked', explode(' ', $levelnode->getAttribute('class')))) {
                    $this->execute('behat_general::i_click_on_in_the',
                        array($selectedlevelxpath, "xpath_element", $this->escape($name), "table_row")
                    );
                }

            } else {

                // Getting the name of the field.
                $radioxpath = $this->get_criterion_xpath($name) .
                    $selectedlevelxpath . "/descendant::input[@type='radio']";
                $radionode = $this->find('xpath', $radioxpath);
                // which will delegate the process to the field type.
                $radionode->setValue($radionode->getAttribute('value'));
            }

            // Setting the remark.

            // First we need to get the textarea name, then we can set the value.
            $textarea = $this->get_node_in_container('css_element', 'textarea', 'table_row', $name);
            $this->execute('behat_forms::i_set_the_field_to', array($textarea->getAttribute('name'), $criterion[1]));
        }
    }

    /**
     * Checks that the level was previously selected and the user changed to another level.
     *
     * @Then /^the level with "(?P<points_number>\d+)" points was previously selected for the rubric criterion "(?P<criterion_name_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $criterionname
     * @param int $points
     * @return void
     */
    public function the_level_with_points_was_previously_selected_for_the_rubric_criterion($points, $criterionname) {

        $levelxpath = $this->get_criterion_xpath($criterionname) .
            $this->get_level_xpath($points) .
            "[contains(concat(' ', normalize-space(@class), ' '), ' currentchecked ')]";

        // Works both for JS and non-JS.
        // - JS: Class -> checked is there when is marked as green.
        // - Non-JS: When editing a rubric definition, there are radio inputs and when viewing a
        //   grade @class contains checked.
        $levelxpath .= "[not(contains(concat(' ', normalize-space(@class), ' '), ' checked '))]" .
            "[not(/descendant::input[@type='radio'][@checked!='checked'])]";

        try {
            $this->find('xpath', $levelxpath);
        } catch (ElementNotFoundException $e) {
            throw new ExpectationException('"' . $points . '" points level was not previously selected', $this->getSession());
        }
    }

    /**
     * Checks that the level is currently selected. Works both when grading rubrics and viewing graded rubrics.
     *
     * @Then /^the level with "(?P<points_number>\d+)" points is selected for the rubric criterion "(?P<criterion_name_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $criterionname
     * @param int $points
     * @return void
     */
    public function the_level_with_points_is_selected_for_the_rubric_criterion($points, $criterionname) {

        $levelxpath = $this->get_criterion_xpath($criterionname) .
            $this->get_level_xpath($points);

        // Works both for JS and non-JS.
        // - JS: Class -> checked is there when is marked as green.
        // - Non-JS: When editing a rubric definition, there are radio inputs and when viewing a
        //   grade @class contains checked.
        $levelxpath .= "[" .
            "contains(concat(' ', normalize-space(@class), ' '), ' checked ')" .
            " or " .
            "/descendant::input[@type='radio'][@checked='checked']" .
            "]";

        try {
            $this->find('xpath', $levelxpath);
        } catch (ElementNotFoundException $e) {
            throw new ExpectationException('"' . $points . '" points level is not selected', $this->getSession());
        }
    }

    /**
     * Checks that the level is not currently selected. Works both when grading rubrics and viewing graded rubrics.
     *
     * @Then /^the level with "(?P<points_number>\d+)" points is not selected for the rubric criterion "(?P<criterion_name_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $criterionname
     * @param int $points
     * @return void
     */
    public function the_level_with_points_is_not_selected_for_the_rubric_criterion($points, $criterionname) {

        $levelxpath = $this->get_criterion_xpath($criterionname) .
            $this->get_level_xpath($points);

        // Works both for JS and non-JS.
        // - JS: Class -> checked is there when is marked as green.
        // - Non-JS: When editing a rubric definition, there are radio inputs and when viewing a
        //   grade @class contains checked.
        $levelxpath .= "[not(contains(concat(' ', normalize-space(@class), ' '), ' checked '))]" .
            "[./descendant::input[@type='radio'][@checked!='checked'] or not(./descendant::input[@type='radio'])]";

        try {
            $this->find('xpath', $levelxpath);
        } catch (ElementNotFoundException $e) {
            throw new ExpectationException('"' . $points . '" points level is selected', $this->getSession());
        }
    }


    /**
     * Makes a hidden rubric field visible (if necessary) and sets a value on it.
     *
     * @param string $name The name of the field
     * @param string $value The value to set
     * @param bool $visible
     * @return void
     */
    protected function set_rubric_field_value($name, $value, $visible = false) {

        // Fields are hidden by default.
        if ($this->running_javascript() == true && $visible === false) {
            $xpath = "//*[@name='$name']/following-sibling::*[contains(concat(' ', normalize-space(@class), ' '), ' plainvalue ')]";
            $textnode = $this->find('xpath', $xpath);
            $textnode->click();
        }

        // Set the value now.
        $description = $this->find_field($name);
        $description->setValue($value);
    }

    /**
     * Performs click confirming the action.
     *
     * @param NodeElement $node
     * @return void
     */
    protected function click_and_confirm($node) {

        // Clicks to perform the action.
        $node->click();

        // Confirms the delete.
        if ($this->running_javascript()) {
            $confirmbutton = $this->get_node_in_container(
                'button',
                get_string('yes'),
                'dialogue',
                get_string('confirmation', 'admin')
            );
            $confirmbutton->click();
        }
    }

    /**
     * Returns the xpath representing a selected level.
     *
     * It is not including the path to the criterion.
     *
     * It is the xpath when grading a rubric or viewing a rubric,
     * it is not the same xpath when editing a rubric.
     *
     * @param int $points
     * @return string
     */
    protected function get_level_xpath($points) {
        return "//td[contains(concat(' ', normalize-space(@class), ' '), ' level ')]" .
            "[./descendant::span[@class='scorevalue'][text()='$points']]";
    }

    /**
     * Returns the xpath representing the selected criterion.
     *
     * It is the xpath when grading a rubric or viewing a rubric,
     * it is not the same xpath when editing a rubric.
     *
     * @param string $criterionname Literal including the criterion name.
     * @return string
     */
    protected function get_criterion_xpath($criterionname) {
        $literal = behat_context_helper::escape($criterionname);
        return "//tr[contains(concat(' ', normalize-space(@class), ' '), ' criterion ')]" .
            "[./descendant::td[@class='description'][text()=$literal]]";
    }
}
