@mod @mod_quiz
Feature: Allow settings to show Max marks and Marks, Max marks only, or hide the grade information completely.
  As a teacher
  In order to decide how grade review options are displayed on questions in a quiz review page
  I need to be able to set the grade review options for a quiz to to show Max and Marks, Max only, or hide the grade information.

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                |
      | student1  | Student   | One      | student1@example.com |
      | teacher   | Teacher   | One      | teacher@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext    |
      | Test questions   | truefalse | TF1  | First question  |
    And the following "activities" exist:
      | activity | name   | intro       | course | idnumber | maxmarksduring | marksduring | maxmarksimmediately | marksimmediately | preferredbehaviour |
      | quiz     | Quiz 1 | Quiz 1 test | C1     | quiz1    | 1              | 1           | 1                   | 1                | immediatefeedback  |
      | quiz     | Quiz 2 | Quiz 2 test | C1     | quiz2    | 0              | 0           | 1                   | 1                | immediatefeedback  |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    | 2.00    |
    And quiz "Quiz 2" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    | 2.00    |

  @javascript
  Scenario: Show max marks and marks during and immediately after the attempt.
    Given I am on the "Quiz 1" "quiz activity" page logged in as "student1"
    And I click on "Attempt quiz" "button"
    And I should see "Question 1" in the ".info" "css_element"
    And I should see "Not complete" in the ".info" "css_element"
    And I should see "Marked out of 2.00" in the ".info" "css_element"
    And I set the field "True" to "1"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    Then I should see "Finished" in the "State" "table_row"
    And I should see "Question 1" in the ".info" "css_element"
    And I should see "Correct" in the ".info" "css_element"
    And I should see "Mark 2.00 out of 2.00" in the ".info" "css_element"

    And I am on the "Quiz 2" "quiz activity" page
    And I click on "Attempt quiz" "button"
    And I should see "Question 1" in the ".info" "css_element"
    And I should see "Not complete" in the ".info" "css_element"
    And I should not see "Marked out of 2.00" in the ".info" "css_element"
    And I set the field "True" to "1"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I should see "Finished" in the "State" "table_row"
    And I should see "Question 1" in the ".info" "css_element"
    And I should see "Correct" in the ".info" "css_element"
    And I should see "Mark 2.00 out of 2.00" in the ".info" "css_element"
