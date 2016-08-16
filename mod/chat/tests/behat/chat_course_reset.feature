@mod @mod_chat
Feature: Chat reset
  In order to reuse past chat activities
  As a teacher
  I need to remove all previous data.

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
    And the following "activities" exist:
      | activity | name           | Description           | course | idnumber |
      | chat     | Test chat name | Test chat description | C1     | chat1    |

  Scenario: Use course reset to update chat start date
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I navigate to "Edit settings" node in "Course administration"
    And I set the following fields to these values:
      | startdate[day]       | 1 |
      | startdate[month]     | January |
      | startdate[year]      | 2020 |
    And I press "Save and display"
    And I follow "Test chat name"
    And I navigate to "Edit settings" node in "Chat administration"
    And I set the following fields to these values:
      | chattime[day]       | 1 |
      | chattime[month]     | January |
      | chattime[year]      | 2020 |
      | chattime[hour]      | 12 |
      | chattime[minute]    | 00 |
    And I press "Save and display"
    When I navigate to "Reset" node in "Course administration"
    And I set the following fields to these values:
      | id_reset_start_date_enabled | 1  |
      | reset_start_date[day]       | 1 |
      | reset_start_date[month]     | January |
      | reset_start_date[year]      | 2030 |
    And I press "Reset course"
    And I should see "Date changed" in the "Chats" "table_row"
    And I press "Continue"
    Then I follow "Course 1"
    And I follow "Test chat name"
    And I navigate to "Edit settings" node in "Chat administration"
    And I expand all fieldsets
    And the "id_chattime_year" select box should contain "2030"
