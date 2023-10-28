@mod @mod_imscp
Feature: Display the IMS content package description in the IMSCP and optionally in the course
  In order to display the the IMS content package description description in the course
  As a teacher
  I need to enable the 'Display description on course page' setting.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course | name                     | intro                                | packagefilepath                             |
      | imscp    | C1     | Test IMS content package | Test IMS content package description | mod/imscp/tests/packages/singelscobasic.zip |

  Scenario: Description is displayed in the IMS content package
    When I am on the "Test IMS content package" "imscp activity" page logged in as teacher1
    Then I should see "Test IMS content package description"

  Scenario: Show IMS description in the course homepage
    When I am on the "Test IMS content package" "imscp activity editing" page logged in as teacher1
    And the following fields match these values:
      | Display description on course page | |
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    Then I should see "Test IMS content package description"

  Scenario: Hide IMS description in the course homepage
    When I am on the "Test IMS content package" "imscp activity editing" page logged in as teacher1
    And the following fields match these values:
      | Display description on course page | |
    And I press "Save and return to course"
    Then I should not see "Test IMS content package description"
