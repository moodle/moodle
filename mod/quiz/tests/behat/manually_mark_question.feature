@mod @mod_quiz
Feature: Teachers can override the grade for any question
  As a teacher
  In order to correct errors
  I must be able to override the grades that Moodle gives.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student0@example.com |
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
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    | defaultmark |
      | Test questions   | essay       | TF1   | First question  | 20          |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | grade |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 20    |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Attempts: 1"
    And I follow "Review attempt"

  @javascript @_switch_window @_bug_phantomjs
  Scenario: Validating the marking of an essay question attempt.
    When I follow "Make comment or override mark"
    And I switch to "commentquestion" window
    And I set the field "Mark" to "25"
    And I press "Save"
    Then I should see "This grade is outside the valid range."
    And I set the field "Mark" to "aa"
    And I press "Save"
    And I should see "That is not a valid number."
    And I set the field "Mark" to "10.0"
    And I press "Save" and switch to main window
    And I should see "Complete" in the "Manually graded 10 with comment: " "table_row"
    # This time is same as time the window is open. So wait for it to close before proceeding.
    And I wait "2" seconds
