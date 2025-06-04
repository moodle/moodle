@core @core_question @report @report_questioninstance
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
    And the following "activities" exist:
      | activity | course | name           |
      | quiz     | C1     | Test quiz Q001 |
    And the following "questions" exist:
      | questioncategory           | qtype       | name |
      | Default for Test quiz Q001 | truefalse   | TF   |
      | Default for Test quiz Q001 | shortanswer | SA   |
    And quiz "Test quiz Q001" contains the following questions:
      | question | page | maxmark |
      | TF       | 1    | 5.0     |
      | SA       | 1    | 5.0     |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel    | reference      | name          |
      | Activity module | Test quiz Q001 | Quiz category |

  Scenario: Generate general and specific report
    Given I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Reports > Question instances" in site administration
    # Checks the dropdown list is in alphabetical order
    And "Matching" "option" should appear before "Select missing words" "option" in the "Question type" "select"
    When I press "Get the report"
    Then "Quiz: Test quiz Q001" row "Total" column of "generaltable" table should contain "2"
    And "Quiz: Test quiz Q001" row "Visible" column of "generaltable" table should contain "2"
    And "Quiz: Test quiz Q001" row "Hidden" column of "generaltable" table should contain "0"

  Scenario: Generate report displaying hidden questions
    Given I am on the "Test quiz Q001" "mod_quiz > question bank" page logged in as "admin"
    And I choose "Delete" action for "TF" in the question bank
    And I press "Delete"
    And I navigate to "Reports > Question instances" in site administration
    When I press "Get the report"
    Then "Quiz: Test quiz Q001" row "Total" column of "generaltable" table should contain "2"
    And "Quiz: Test quiz Q001" row "Visible" column of "generaltable" table should contain "1"
    And "Quiz: Test quiz Q001" row "Hidden" column of "generaltable" table should contain "1"
    And I set the field "menuqtype" to "True/False"
    And I press "Get the report"
    And "Quiz: Test quiz Q001" row "Total" column of "generaltable" table should contain "1"
    And "Quiz: Test quiz Q001" row "Visible" column of "generaltable" table should contain "0"
    And "Quiz: Test quiz Q001" row "Hidden" column of "generaltable" table should contain "1"
