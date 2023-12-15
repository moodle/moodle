@communication
Feature: Access the communication configuration page
  As an editing teacher
  See dynamic form fields based on selected provider

  Background: Set up teachers and course for the communication confifiguration page
    Given I enable communication experimental feature
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "courses" exist:
      | fullname    | shortname   | category | selectedcommunication |
      | Test course | Test course | 0        | none                  |
    And the following "course enrolments" exist:
      | user     | course      | role           |
      | teacher1 | Test course | editingteacher |
      | teacher2 | Test course | teacher        |

  Scenario: A teacher with the correct capability can access the communication configuration page
    Given I am on the "Test course" "Course" page logged in as "teacher1"
    When I navigate to "Communication" in current page administration
    Then I should see "Communication"

  Scenario: A teacher without the correct capability cannot access the communication configuration page
    Given I am on the "Test course" "Course" page logged in as "teacher2"
    Then "Communication" "link" should not exist in current page administration

  Scenario: I cannot see the communication link when communication provider is disabled
    Given I disable communication experimental feature
    And I am on the "Test course" "Course" page logged in as "teacher1"
    Then "Communication" "link" should not exist in current page administration

  @javascript
  Scenario: The communication form fields toggle dynamically when valid provider is set
    Given a Matrix mock server is configured
    And I am on the "Test course" "Course" page logged in as "teacher1"
    When I navigate to "Communication" in current page administration
    And I set the following fields to these values:
      | selectedcommunication | communication_matrix |
    Then I should see "Room name"
    And I should see "Room topic"
