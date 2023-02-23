@mod @mod_quiz @core_completion
Feature: View activity completion in the quiz activity
  In order to have visibility of quiz completion requirements
  As a student
  I need to be able to view my quiz completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | grade_item_advanced | hiddenuntil |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activity" exists:
      | activity                     | quiz           |
      | course                       | C1             |
      | idnumber                     | quiz1          |
      | name                         | Test quiz name |
      | section                      | 1              |
      | attempts                     | 2              |
      | gradepass                    | 5.00           |
      | completion                   | 2              |
      | completionview               | 1              |
      | completionusegrade           | 1              |
      | completionpassgrade          | 1              |
      | completionattemptsexhausted  | 1              |
      | completionminattemptsenabled | 1              |
      | completionminattempts        | 1              |
    And quiz "Test quiz name" contains the following questions:
      | question       | page |
      | First question | 1    |

  Scenario Outline: View automatic completion items as a student
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test quiz name"
    And the "View" completion condition of "Test quiz name" is displayed as "done"
    And the "Make attempts: 1" completion condition of "Test quiz name" is displayed as "todo"
    And the "Receive a grade" completion condition of "Test quiz name" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Test quiz name" is displayed as "todo"
    And the "Receive a pass grade or complete all available attempts" completion condition of "Test quiz name" is displayed as "todo"
    And user "student1" has attempted "Test quiz name" with responses:
      | slot | response |
      |   1  | False    |
    And I am on "Course 1" course homepage
    And I follow "Test quiz name"
    And the "View" completion condition of "Test quiz name" is displayed as "done"
    And the "Make attempts: 1" completion condition of "Test quiz name" is displayed as "done"
    And the "Receive a grade" completion condition of "Test quiz name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test quiz name" is displayed as "failed"
    And the "Receive a pass grade or complete all available attempts" completion condition of "Test quiz name" is displayed as "todo"
    And I press "Re-attempt quiz"
    And I set the field "<answer>" to "1"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I follow "Finish review"
    And the "View" completion condition of "Test quiz name" is displayed as "done"
    And the "Make attempts: 1" completion condition of "Test quiz name" is displayed as "done"
    And the "Receive a grade" completion condition of "Test quiz name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test quiz name" is displayed as "<passcompletionexpected>"
    And the "Receive a pass grade or complete all available attempts" completion condition of "Test quiz name" is displayed as "done"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "<expectedactivitycompletion>" "icon" should exist in the "Student 1" "table_row"

    Examples:
      | answer | passcompletionexpected | expectedactivitycompletion             |
      | False  | failed                 | Completed (did not achieve pass grade) |
      | True   | done                   | Completed (achieved pass grade)        |
