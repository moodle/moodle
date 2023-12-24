@communication @communication_matrix @javascript
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

  Scenario: I can add room name for matrix room
    Given a Matrix mock server is configured
    And I log in as "teacher1"
    And I am on "Test course" course homepage
    When I navigate to "Communication" in current page administration
    And I set the field "id_selectedcommunication" to "Matrix"
    And I wait to be redirected
    And I should see "Room name"
    And I set the field "id_communicationroomname" to "Sampleroomname"
    And I press "Save changes"
    And I navigate to "Communication" in current page administration
    Then the field "id_communicationroomname" matches value "Sampleroomname"

  Scenario: I can add room topic for matrix room
    Given a Matrix mock server is configured
    And I log in as "teacher1"
    And I am on "Test course" course homepage
    When I navigate to "Communication" in current page administration
    And I set the field "id_selectedcommunication" to "Matrix"
    And I wait to be redirected
    And I should see "Room name"
    And I should see "Room topic"
    And I set the field "id_communicationroomname" to "Sampleroomname"
    And I set the field "id_matrixroomtopic" to "Sampleroomtopic"
    And I press "Save changes"
    And I navigate to "Communication" in current page administration
    Then the field "id_communicationroomname" matches value "Sampleroomname"
    And I press "Cancel"
    And I run all adhoc tasks
    And I navigate to "Communication" in current page administration
    And the field "id_matrixroomtopic" matches value "Sampleroomtopic"
