@core @core_question
Feature: An activity module instance with questions in its context can be deleted
  In order to delete an activity module from the course
  As a teacher
  I need to be able to delete the activity even if it has questions created in its context

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  @javascript
  Scenario: Synchronously deleting a quiz with existing questions in its context
    Given the following config values are set as admin:
      | coursebinenable | 0 | tool_recyclebin |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name | Test quiz Q001 |
    And I add a "True/False" question to the "Test quiz" quiz with:
      | Category      | Default for Test quiz Q001       |
      | Question name | Test used question to be deleted |
      | Question text | Write about whatever you want    |
    And I am on "Course 1" course homepage
    When I delete "Test quiz Q001" activity
    Then I should not see "Test quiz Q001"
