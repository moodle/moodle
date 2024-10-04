@mod @mod_assign
Feature: In an assignment, teachers can download submissions through the actions dropdown
  In order to download all submissions in an assignment
  As a teacher
  I need to have the option available in the actions dropdown menu

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | student1  | C1      | student         |

  Scenario: The option to download all submissions is available when there are submissions.
    Given the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | assignsubmission_onlinetext_enabled | 1                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                       |
      | Test assignment name  | student1  | I'm the student first submission |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I change window size to "large"
    When I navigate to "Submissions" in current page administration
    Then the "Download all submissions" item should exist in the "Actions" action menu

  Scenario Outline: Option to download all submissions is unavailable if no submissions have been made.
    Given the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | assignsubmission_onlinetext_enabled | <onlinetext_enabled>    |
      | file_enabled                        | <file_enabled>          |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I change window size to "large"
    When I navigate to "Submissions" in current page administration
    Then the "Download all submissions" item should not exist in the "Actions" action menu

    Examples:
      | onlinetext_enabled | file_enabled |
      | 1                  | 0            |
      | 0                  | 0            |
