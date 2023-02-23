@tool @tool_recyclebin
Feature: Backup user data
  As a teacher
  I want user data to be backed up when I delete a course module
  So that I can recover student content

  Background: Course with teacher and student exist.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher@asd.com |
      | student1 | Student | 1 | student@asd.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | coursebinenable | 1 | tool_recyclebin |
      | autohide | 0 | tool_recyclebin |
    And the following "activities" exist:
      | activity | course | section | name   | intro                 |
      | quiz     | C1     | 1       | Quiz 1 | Test quiz description |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext    |
      | Test questions   | truefalse | TF1  | First question  |
      | Test questions   | truefalse | TF2  | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 1    |

  @javascript
  Scenario Outline: Delete and restore a quiz with user data
    Given the following config values are set as admin:
      | restore_general_users | <include_user> | restore |
    And I am on the "Quiz 1" "quiz activity" page logged in as student1
    And I press "Attempt quiz"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "False" "radio" in the "Second question" "question"
    And I press "Finish attempt"
    And I press "Submit all and finish"
    And I click on "Submit" "button" in the "Submit all your answers and finish?" "dialogue"
    And I should see "50.00 out of 100.00"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I delete "Quiz 1" activity
    And I run all adhoc tasks
    And I navigate to "Recycle bin" in current page administration
    And I should see "Quiz 1"
    And I click on "Restore" "link" in the "region-main" "region"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I navigate to "User report" in the course gradebook
    Then "Quiz 1" row "Grade" column of "user-grade" table should contain "50"
    And "Quiz 1" row "Percentage" column of "user-grade" table should contain "50"

    Examples:
      | include_user | case_explanation |
      | 1            | Checked          |
      | 1            | Unchecked        |
