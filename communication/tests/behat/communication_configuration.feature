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
    And I wait to be redirected
    Then I should see "Room name"
    And I should see "Room topic"

  @javascript
  Scenario: Changing the communication provider in the form fetches the correct data
    Given a Matrix mock server is configured
    And I am on the "Test course" "Course" page logged in as "teacher1"
    When I navigate to "Communication" in current page administration
    And I set the following fields to these values:
      | selectedcommunication        | communication_matrix |
    And I wait to be redirected
    And I should see "Room name"
    And I should see "Room topic"
    And I set the following fields to these values:
      | communication_matrixroomname | Matrix room  |
      | matrixroomtopic              | Matrix topic |
    And I click on "Save changes" "button"
    And I navigate to "Communication" in current page administration
    Then the field "Room name" matches value "Matrix room"
    And the field "Room topic" matches value "Matrix topic"
    And I set the following fields to these values:
      | selectedcommunication        | communication_customlink |
    And I wait to be redirected
    And I should see "Room name"
    And I should not see "Room topic"
    And I should see "Custom link URL"
    And I set the following fields to these values:
      | communication_customlinkroomname | Custom link room   |
      | customlinkurl                    | https://moodle.org |
    And I click on "Save changes" "button"
    And I navigate to "Communication" in current page administration
    And the field "Room name" matches value "Custom link room"
    And the field "Custom link URL" matches value "https://moodle.org"
    And I set the following fields to these values:
      | selectedcommunication        | communication_matrix |
    And I wait to be redirected
    And I should see "Room name"
    And I should see "Room topic"
    And the field "Room name" matches value "Matrix room"
    And the field "Room topic" matches value "Matrix topic"
    And I should not see "Custom link URL"
    And I set the following fields to these values:
      | selectedcommunication        | communication_customlink |
    And I wait to be redirected
    And I should see "Room name"
    And I should see "Custom link URL"
    And the field "Room name" matches value "Custom link room"
    And the field "Custom link URL" matches value "https://moodle.org"
    And I should not see "Room topic"
    And I set the following fields to these values:
      | selectedcommunication        | communication_matrix |
    And I wait to be redirected
    And I click on "Save changes" "button"
    And I am on "Test course" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and display"
    And I navigate to "Communication" in current page administration
    And the field "Room name" matches value "Matrix room"
    And the field "Room topic" matches value "Matrix topic"
    And I press "Cancel"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Group mode | Visible groups |
    And I navigate to "Communication" in current page administration
    And the field "Room name" matches value "Matrix room"
    And the field "Room topic" matches value "Matrix topic"

  @javascript
  Scenario: Emptying the room name field always sets course name as default
    Given a Matrix mock server is configured
    And I am on the "Test course" "Course" page logged in as "teacher1"
    When I navigate to "Communication" in current page administration
    And I set the following fields to these values:
      | selectedcommunication        | communication_matrix |
    And I wait to be redirected
    And I should see "Room name"
    And I should see "Room topic"
    And I set the following fields to these values:
      | communication_matrixroomname | Matrix room  |
      | matrixroomtopic              | Matrix topic |
    And I click on "Save changes" "button"
    And I navigate to "Communication" in current page administration
    Then the field "Room name" matches value "Matrix room"
    And the field "Room topic" matches value "Matrix topic"
    And I set the following fields to these values:
      | communication_matrixroomname | |
    And I click on "Save changes" "button"
    And I navigate to "Communication" in current page administration
    And the field "Room name" matches value "Test course"
