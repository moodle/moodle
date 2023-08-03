@communication @communication_matrix @javascript
Feature: Communication matrix
  Access the Matrix room using the link provided
  As a student or a teacher

  Background: Make sure the mock server is initialized and a course is created
    Given a Matrix mock server is configured
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname    | shortname   | category | selectedcommunication | communicationroomname |
      | Test course | Test course | 0        | communication_matrix  | matrixroom            |
    And the following "course enrolments" exist:
      | user     | course      | role           |
      | teacher1 | Test course | editingteacher |
      | student1 | Test course | student        |
    And I run all adhoc tasks

  Scenario: I can access the matrix room using the room link icon as a teacher
    Given I am on the "Test course" "Course" page logged in as "teacher1"
    Then ".btn-footer-communication" "css_element" should be visible
    And I change window size to "mobile"
    Then ".btn-footer-communication" "css_element" should not be visible
    # You should not be able to see the following line unless you are in Mobile view.
    # Behat is currently setup to always show footer links.
    And ".footer-link-communication" "css_element" should be visible

  Scenario: I can access the matrix room using the room link icon as a student
    Given I am on the "Test course" "Course" page logged in as "student1"
    Then ".btn-footer-communication" "css_element" should be visible
    And I change window size to "mobile"
    Then ".btn-footer-communication" "css_element" should not be visible
    # You should not be able to see the following line unless you are in Mobile view.
    # Behat is currently setup to always show footer links.
    And ".footer-link-communication" "css_element" should be visible

  Scenario: I cannot see the matrix room link when communication provider is disabled
    Given I am on the "Test course" "Course" page logged in as "teacher1"
    When I navigate to "Communication" in current page administration
    And I set the following fields to these values:
      | selectedcommunication | none |
    And I press "Save changes"
    And I run all adhoc tasks
    And I reload the page
    Then ".btn-footer-communication" "css_element" should not be visible
    And I change window size to "mobile"
    And ".footer-link-communication" "css_element" should not be visible
