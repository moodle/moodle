@qtype @qtype_ordering
Feature: Complete an Ordering question attempt
  As a learner
  In order pass a quiz with ordering question types
  I need to submit an attempt and be graded

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@moodle.com  |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    # Layouttype is a finicky setting afaik, so we'll just leave it vertical for now.
    And the following "questions" exist:
      | questioncategory | qtype    | name         | template | layouttype | selecttype | selectcount | gradingtype | showgrading | numberingstyle |
      | Test questions   | ordering | ordering-001 | moodle   | 0          | 0          | 3           | -1          | 1           | none           |
      | Test questions   | ordering | ordering-002 | moodle   | 0          | 0          | 3           | 1           | 0           | abc            |
      | Test questions   | ordering | ordering-003 | moodle   | 0          | 1          | 6           | 2           | 1           | ABCD           |
      | Test questions   | ordering | ordering-004 | moodle   | 0          | 2          | 6           | 3           | 0           | 123            |
      | Test questions   | ordering | ordering-005 | moodle   | 0          | 0          | 3           | 4           | 1           | iii            |
      | Test questions   | ordering | ordering-006 | moodle   | 0          | 0          | 3           | 5           | 0           | IIII           |
      | Test questions   | ordering | ordering-007 | moodle   | 0          | 0          | 3           | 6           | 1           | abc            |
      | Test questions   | ordering | ordering-008 | moodle   | 0          | 0          | 3           | 7           | 0           | abc            |
    And the following "activities" exist:
      | activity | name   | intro       | course | idnumber | maxmarksduring | marksduring | maxmarksimmediately | marksimmediately | preferredbehaviour |
      | quiz     | Quiz 1 | Quiz 1 test | C1     | quiz1    | 1              | 1           | 1                   | 1                | immediatefeedback  |

  @javascript
  Scenario Outline: As a student I can partially answer the question and get different grades and feedback.
    Given quiz "Quiz 1" contains the following questions:
      | question   | page | maxmark |
      | <question> | 1    | 2.00    |
    And I am on the "Quiz 1" "quiz activity" page logged in as "student1"
    When I click on "Attempt quiz" "button"
    And I wait until the page is ready
    # Confirm the appropriate styling class is applied to the list items.
    And I should see "Modular" in the "<styling>" "css_element"
    # DnD the items in the semi-correct order and then submit.
    And I drag "Environment" to space "1" in the ordering question
    And I drag "Learning" to space "2" in the ordering question
    And I drag "Oriented" to space "3" in the ordering question
    And I drag "Dynamic" to space "4" in the ordering question
    And I drag "Object" to space "5" in the ordering question
    And I drag "Modular" to space "6" in the ordering question
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    # Confirm the attempt was graded correctly.
    Then I should see "Finished" in the "Status" "table_row"
    And I should see "Question 1" in the ".info" "css_element"
    And I should see "<state>" in the ".info" "css_element"
    And I should see "<mark>" in the ".info" "css_element"
    And I change window size to "large"
    # Confirm that the grade details shows some of the formula as behat does not like /.
    And I should <expect> "<gradedetails>"
    And I should see "<correct>"
    And I should see "<partial>"
    And I should see "<incorrect>"
    And I should <expect> "<type>"
    And I log out
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And the following should exist in the "user-grades" table:
      | -1-       | -2-                  | -3-     |
      | Student 1 | student1@example.com | <grade> |
    Examples:
      | question     | styling        | state             | mark                  | grade | expect  | gradedetails      | correct          | partial                    |  incorrect         | type                                         |
      | ordering-001 | .numberingnone | Incorrect         | Mark 0.00 out of 2.00 | 0.00  | see     | not right at all. |                  |                            |                    | All or nothing                               |
      | ordering-002 | .numberingabc  | Partially correct | Mark 0.40 out of 2.00 | 20.00 | not see | Grade details:    | Correct items: 1 |                            | Incorrect items: 4 | Absolute position                            |
      | ordering-003 | .numberingABCD | Partially correct | Mark 0.33 out of 2.00 | 16.67 | see     | 6 = 17%           | Correct items: 1 |                            | Incorrect items: 5 | Relative to the next item (including last)   |
      | ordering-004 | .numbering123  | Partially correct | Mark 0.33 out of 2.00 | 16.67 | not see | Grade details:    |                  | Partially correct items: 2 | Incorrect items: 4 | Relative to the next item (excluding last)   |
      | ordering-005 | .numberingiii  | Partially correct | Mark 0.13 out of 2.00 | 6.67  | see     | 30 = 7%           |                  | Partially correct items: 2 | Incorrect items: 4 | Relative to ALL the previous and next items  |
      | ordering-006 | .numberingIIII | Partially correct | Mark 0.67 out of 2.00 | 33.33 | not see | Grade details:    | Correct items: 2 |                            | Incorrect items: 4 | Relative to both the previous and next items |
      | ordering-007 | .numberingabc  | Partially correct | Mark 0.67 out of 2.00 | 33.33 | see     | 6 = 33%           | Correct items: 2 |                            | Incorrect items: 4 | Longest contiguous subset                    |
      | ordering-008 | .numberingabc  | Partially correct | Mark 0.93 out of 2.00 | 46.67 | not see | Grade details:    | Correct items: 2 | Partially correct items: 2 | Incorrect items: 2 | Longest ordered subset                       |

  @javascript
  Scenario: As a student I can get hints when attempting the question.
    And the following "activities" exist:
      | activity | name   | intro       | course | preferredbehaviour |
      | quiz     | Quiz 2 | Quiz 2 test | C1     | interactive        |
    Given quiz "Quiz 2" contains the following questions:
      | question     | page | maxmark |
      | ordering-003 | 1    | 2.00    |
    And I am on the "Quiz 2" "quiz activity" page logged in as "student1"
    When I click on "Attempt quiz" "button"
    And I change window size to "large"
    And I wait until the page is ready
    And I drag "Environment" to space "1" in the ordering question
    And I drag "Learning" to space "2" in the ordering question
    And I drag "Dynamic" to space "3" in the ordering question
    And I drag "Oriented" to space "4" in the ordering question
    And I drag "Object" to space "5" in the ordering question
    And I drag "Modular" to space "6" in the ordering question
    And I press "Check"
    Then I should see "That is not right at all."
    And I should see "Hint 1"
    # Attempt question again.
    And I press "Try again"
    And I wait until the page is ready
    # DnD the items in the correct order and then submit.
    And I drag "Environment" to space "1" in the ordering question
    And I drag "Learning" to space "2" in the ordering question
    And I drag "Dynamic" to space "3" in the ordering question
    And I drag "Oriented" to space "4" in the ordering question
    And I drag "Object" to space "5" in the ordering question
    And I drag "Modular" to space "6" in the ordering question
    And I press "Check"
    And I should see "That is not right at all."
    And I should see "Hint 2"
    # Attempt question again with correct answers.
    And I press "Try again"
    And I wait until the page is ready
    # DnD the items in the correct order and then submit.
    And I drag "Modular" to space "1" in the ordering question
    And I drag "Object" to space "2" in the ordering question
    And I drag "Oriented" to space "3" in the ordering question
    And I drag "Dynamic" to space "4" in the ordering question
    And I drag "Learning" to space "5" in the ordering question
    And I drag "Environment" to space "6" in the ordering question
    And I press "Check"
    And I should see "Well done!"
