@mod @mod_survey
Feature: The default introduction is displayed when the activity description is empty

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
      | activity | name             | course | idnumber  | template |
      | survey   | Test survey name | C1     | survey1   | 1        |

  Scenario: Display the default survey introduction when activity description is empty
    Given I am on the "Test survey name" "survey activity" page logged in as "teacher1"
    And I should see "Test survey 1"
    When I am on the "Test survey name" "survey activity editing" page
    And I set the following fields to these values:
      | Description |  |
    And I press "Save and display"
    Then I should see "The purpose of this survey is to help us understand"
    And I am on the "Test survey name" "survey activity editing" page
    And I set the following fields to these values:
      | Survey type | ATTLS (20 item version) |
    And I press "Save and display"
    And I should see "The purpose of this questionnaire is to help us evaluate"
