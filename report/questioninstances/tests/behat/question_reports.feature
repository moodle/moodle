@core @core_question @javascript @report @report_questioninstance
Feature: A Teacher can generate question instance reports
  In order to see question instance reports
  As a Teacher
  I need to generate them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity | course | name           |
      | quiz     | C1     | Test quiz Q001 |
    And the following "questions" exist:
      | questioncategory | qtype       | name  |
      | Test questions   | truefalse   | TF    |
      | Test questions   | shortanswer | SA    |
    And quiz "Test quiz Q001" contains the following questions:
      | question | page | maxmark |
      | TF       | 1    | 5.0     |
      | SA       | 1    | 5.0     |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel    | reference      | name              |
      | Activity module | Test quiz Q001 | Quiz category     |

  @javascript
  Scenario: Generate general and specific report
    Given I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Reports > Question instances" in site administration
    When I press "Get the report"
    Then "Course: Course 1" row "Total" column of "generaltable" table should contain "2"
    And "Course: Course 1" row "Visible" column of "generaltable" table should contain "2"
    And "Course: Course 1" row "Hidden" column of "generaltable" table should contain "0"

  @javascript
  Scenario: Generate report displaying hidden questions
    Given I am on the "Test quiz Q001" "quiz activity" page logged in as "admin"
    And I navigate to "Question bank" in current page administration
    And I click on "Edit" "link" in the "TF" "table_row"
    And I choose "Delete" in the open action menu
    And I press "Delete"
    And I navigate to "Reports > Question instances" in site administration
    When I press "Get the report"
    Then "Course: Course 1" row "Total" column of "generaltable" table should contain "2"
    And "Course: Course 1" row "Visible" column of "generaltable" table should contain "1"
    And "Course: Course 1" row "Hidden" column of "generaltable" table should contain "1"
    And I click on "menuqtype" "select"
    And I click on "True/False" "option"
    And I press "Get the report"
    And "Course: Course 1" row "Total" column of "generaltable" table should contain "1"
    And "Course: Course 1" row "Visible" column of "generaltable" table should contain "0"
    And "Course: Course 1" row "Hidden" column of "generaltable" table should contain "1"
