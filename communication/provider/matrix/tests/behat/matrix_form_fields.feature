@communication @communication_matrix
Feature: Communication matrix form field
  In order to create a new communication room in matrix
  As a teacher
  I can update the room the information from course

  Background: Make sure the mock server is initialized and a course is created for teacher
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname    | shortname   | category |
      | Test course | Test course | 0        |
    And the following "course enrolments" exist:
      | user     | course      | role    |
      | teacher1 | Test course | editingteacher |

  @javascript
  Scenario: I can add room name and topic for matrix room
    Given a Matrix mock server is configured
    And I am on the "Test course" "Course" page logged in as "teacher1"
    When I navigate to "Communication" in current page administration
    And I set the following fields to these values:
      | selectedcommunication | communication_matrix |
    And I wait to be redirected
    And I set the following fields to these values:
      | communication_matrixroomname | Sampleroomname  |
      | matrixroomtopic              | Sampleroomtopic |
    And I should see "Room name"
    And I should see "Room topic"
    And I press "Save changes"
    And I navigate to "Communication" in current page administration
    Then the field "Room name" matches value "Sampleroomname"
    And the field "Room topic" matches value "Sampleroomtopic"
