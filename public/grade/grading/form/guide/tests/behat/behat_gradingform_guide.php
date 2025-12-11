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
 * Steps definitions for marking guides.
 *
 * @package   gradingform_guide
 * @category  test
 * @copyright 2015 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions to help with marking guides.
 *
 * @package   gradingform_guide
 * @category  test
 * @copyright 2015 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_gradingform_guide extends behat_base {

    /**
     * Defines the marking guide with the provided data, following marking guide's definition grid cells.
     *
     * This method fills the marking guide of the marking guide definition
     * form; the provided TableNode should contain one row for
     * each criterion and each cell of the row should contain:
     * # Criterion name, a.k.a. shortname
     * # Description for students
     * # Description for markers
     * # Max score
     *
     * Works with both JS and non-JS.
     *
     * @When /^I define the following marking guide:$/
     * @throws ExpectationException
     * @param TableNode $guide
     */
    public function i_define_the_following_marking_guide(TableNode $guide) {
        $steptableinfo = '| Criterion name | Description for students | Description for markers | Maximum score |';

        if ($criteria = $guide->getHash()) {
            $addcriterionbutton = $this->find_button(get_string('addcriterion', 'gradingform_guide'));

            foreach ($criteria as $index => $criterion) {
                // Make sure the criterion array has 4 elements.
                if (count($criterion) != 4) {
                    throw new ExpectationException(
                        'The criterion definition should contain name, description for students and markers, and maximum points. ' .
                        'Please follow this format: ' . $steptableinfo,
                        $this->getSession()
                    );
                }

                // On load, there's already a criterion template ready.
                $shortnamevisible = false;
                if ($index > 0) {
                    // So if the index is greater than 0, we click the Add new criterion button to add a new criterion.
                    $addcriterionbutton->click();
                    $shortnamevisible = true;
                }

                $criterionroot = 'guide[criteria][NEWID' . ($index + 1) . ']';

                // Set the field value for the Criterion name.
                $this->set_guide_field_value($criterionroot . '[shortname]', $criterion['Criterion name'], $shortnamevisible);

                // Set the field value for the Description for students field.
                $this->set_guide_field_value($criterionroot . '[description]', $criterion['Description for students']);

                // Set the field value for the Description for markers field.
                $this->set_guide_field_value($criterionroot . '[descriptionmarkers]', $criterion['Description for markers']);

                // Set the field value for the Max score field.
                $this->set_guide_field_value($criterionroot . '[maxscore]', $criterion['Maximum score']);
            }
        }
    }

    /**
     * Edits an existing marking guide with the provided data.
     *
     * This method edits the marking guide of the marking guide definition
     * form; the provided TableNode should contain one row for
     * each field and each cell of the row should contain:
     * | Field name          | New value               |
     * | shortname           | Updated Grade criterion |
     *
     * @When /^I edit the marking guide criterion "([^"]*)" with the following values:$/
     * @param string $criterionname
     * @param TableNode $fields
     */
    public function i_edit_the_marking_guide_criterion_with_the_following_values(string $criterionname, TableNode $fields) {
        if ($fieldvalues = $fields->getHash()) {
            $criterionid = 0;
            $locator = "//tr[contains(@class, 'criterion')]//div[@class='criterionname']"
                     . "//span[@class='textvalue'][text()='$criterionname']/ancestor::tr";
            if ($criterionrow = $this->find('xpath', $locator)) {
                $criterionid = str_replace('guide-criteria-', '', $criterionrow->getAttribute('id'));
            }

            if ($criterionid) {
                $criterionroot = 'guide[criteria]' . '[' . $criterionid . ']';

                foreach ($fieldvalues as $fieldvalue) {
                    // Make sure the fieldvalue array has 2 elements.
                    if (count($fieldvalue) != 2) {
                        throw new ExpectationException(
                            'The field definition should contain field name and new value. ' .
                            'Please follow this format: | Field name | New value |',
                            $this->getSession()
                        );
                    }

                    $fieldname = $fieldvalue['Field name'];
                    $newvalue = $fieldvalue['New value'];

                    $this->set_guide_field_value($criterionroot . "[$fieldname]", $newvalue);
                }
            } else {
                throw new ExpectationException(
                    'Criterion with name "' . $criterionname . '" not found.',
                    $this->getSession()
                );
            }
        }
    }

    /**
     * Defines the marking guide with the provided data, following marking guide's definition grid cells.
     *
     * This method fills the table of frequently used comments of the marking guide definition form.
     * The provided TableNode should contain one row for each frequently used comment.
     * Each row contains:
     * # Comment
     *
     * Works with both JS and non-JS.
     *
     * @When /^I define the following frequently used comments:$/
     * @throws ExpectationException
     * @param TableNode $commentstable
     */
    public function i_define_the_following_frequently_used_comments(TableNode $commentstable) {
        $steptableinfo = '| Comment |';

        if ($comments = $commentstable->getRows()) {
            $addcommentbutton = $this->find_button(get_string('addcomment', 'gradingform_guide'));

            foreach ($comments as $index => $comment) {
                // Make sure the comment array has only 1 element.
                if (count($comment) != 1) {
                    throw new ExpectationException(
                        'The comment cannot be empty. Please follow this format: ' . $steptableinfo,
                        $this->getSession()
                    );
                }

                // On load, there's already a comment template ready.
                $commentfieldvisible = false;
                if ($index > 0) {
                    // So if the index is greater than 0, we click the Add frequently used comment button to add a new criterion.
                    $addcommentbutton->click();
                    $commentfieldvisible = true;
                }

                $commentroot = 'guide[comments][NEWID' . ($index + 1) . ']';

                // Set the field value for the frequently used comment.
                $this->set_guide_field_value($commentroot . '[description]', $comment[0], $commentfieldvisible);
            }
        }
    }

    /**
     * Performs grading of the student by filling out the marking guide.
     * Set one line per criterion and for each criterion set "| Criterion name | Points | Remark |".
     *
     * @When /^I grade by filling the marking guide with:$/
     *
     * @throws ExpectationException
     * @param TableNode $guide
     * @return void
     */
    public function i_grade_by_filling_the_marking_guide_with(TableNode $guide) {

        $criteria = $guide->getRowsHash();

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

            $criterionid = 0;
            if ($criterionnamediv = $this->find('xpath', "//div[@class='criterionshortname'][text()='$name']")) {
                $criteriondivname = $criterionnamediv->getAttribute('name');
                // Criterion's name is of the format "advancedgrading[criteria][ID][shortname]".
                // So just explode the string with "][" as delimiter to extract the criterion ID.
                if ($nameparts = explode('][', $criteriondivname)) {
                    $criterionid = $nameparts[1];
                }
            }

            if ($criterionid) {
                $criterionroot = 'advancedgrading[criteria]' . '[' . $criterionid . ']';

                $this->execute('behat_forms::i_set_the_field_to', array($criterionroot . '[score]', $points));

                $this->execute('behat_forms::i_set_the_field_to', array($criterionroot . '[remark]', $criterion[1]));
            }
        }
    }

    /**
     * Makes a hidden marking guide field visible (if necessary) and sets a value on it.
     *
     * @param string $name The name of the field
     * @param string $value The value to set
     * @param bool $visible
     * @return void
     */
    protected function set_guide_field_value($name, $value, $visible = false) {
        // Fields are hidden by default.
        if ($this->running_javascript() && $visible === false) {
            $xpath = "//*[@name='$name']/following-sibling::*[contains(concat(' ', normalize-space(@class), ' '), ' plainvalue ')]";
            $textnode = $this->find('xpath', $xpath);
            $textnode->click();
        }

        // Set the value now.
        $field = $this->find_field($name);
        $field->setValue($value);
    }
}
