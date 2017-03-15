@mod @mod_chat
Feature: Chat calendar entries
  In order to notify students of upcoming chat sessons
  As a teacher
  I need to create a chat activity and publish the event times

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Tina | Teacher1 | teacher1@example.com |
      | student1 | Sam | Student1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Create a chat activity and do not publish the start date to the calendar
    Given the following "activities" exist:
      | activity | name           | intro                 | course | idnumber | schedule |
      | chat     | Test chat name | Test chat description | C1     | chat1    | 0 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Calendar" node in "Site pages"
    Then I should not see "Test chat name"

  Scenario: Create a chat activity and publish the start date to the calendar
    Given the following "activities" exist:
      | activity | name           | intro                 | course | idnumber | schedule |
      | chat     | Test chat name | Test chat description | C1     | chat1    | 1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Calendar" node in "Site pages"
    Then I should see "Test chat name"
