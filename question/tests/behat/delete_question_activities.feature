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

  Scenario: Synchronously deleting a quiz with existing questions in its context
    Given the following config values are set as admin:
      | coursebinenable | 0 | tool_recyclebin |
    And the following "activity" exists:
      | activity | quiz           |
      | course   | C1             |
      | name     | Test quiz Q001 |
    And the following "question categories" exist:
      | contextlevel    | reference      | name                       |
      | Activity module | Test quiz Q001 | Default for Test quiz Q001 |
    And the following "questions" exist:
      | questioncategory           | qtype     | name                             | questiontext                  |
      | Default for Test quiz Q001 | truefalse | Test used question to be deleted | Write about whatever you want |
    And quiz "Test quiz Q001" contains the following questions:
      | question                         | page |
      | Test used question to be deleted | 1    |
    And I am on the "Course 1" course page logged in as teacher1
    And I am on "Course 1" course homepage with editing mode on
    When I delete "Test quiz Q001" activity
    Then I should not see "Test quiz Q001"
