@block @block_completion_progress @mod_quiz
Feature: Using block completion progress for a quiz
  In order to know what quizzes are due
  As a student
  I can visit my dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following config values are set as admin:
      | enablecompletion | 1 |
      | enableavailability | 1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name                    | timeclose  | enablecompletion |
      | quiz     | C1     | Q1A      | Quiz 1A No deadline     | 0          | 1                |
      | quiz     | C1     | Q1B      | Quiz 1B Past deadline   | 1337       | 1                |
      | quiz     | C1     | Q1C      | Quiz 1C Future deadline | 9000000000 | 1                |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | qtype     | name           | questiontext              | questioncategory |
      | truefalse | First question | Answer the first question | Test questions   |
    And quiz "Quiz 1A No deadline" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Quiz 1B Past deadline" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Quiz 1C Future deadline" contains the following questions:
      | question       | page |
      | First question | 1    |
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I follow "Quiz 1A No deadline"
    And I navigate to "Edit settings" node in "Quiz administration"
    And I set the following fields to these values:
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view | 1 |
    And I press "Save and return to course"
    And I add the "Completion Progress" block
    And I configure the "Completion Progress" block
    And I set the following fields to these values:
      | Show percentage to students | Yes |
    And I press "Save changes"
    And I log out

  Scenario: Basic functionning of the block
    Given I log in as "student1"
    And I follow "Course 1"
    Then I should see "Progress: 0%" in the "Completion Progress" "block"

  Scenario: Submit the quizzes
    Given I log in as "student1"
    And I follow "Course 1"
    And I follow "Quiz 1A No deadline"
    And I press "Attempt quiz now"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I follow "C1"
    Then I should see "Progress: 100%" in the "Completion Progress" "block"