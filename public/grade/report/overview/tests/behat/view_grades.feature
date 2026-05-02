@gradereport @gradereport_overview
Feature: Grade overview report can be viewed
  In order to see the courses I am enrolled in
  As a user
  I should be able to access the grade overview report's index page

  Background:
    Given the following "courses" exist:
      | fullname        | shortname |
      | Awesome course  | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | teacher1 | Teacher   | 1        | t1@example.com  |
      | student1 | Student   | 1        | s1@example.com  |
    And the following "course enrolments" exist:
      | user     | course | role            |
      | teacher1 | C1     | editingteacher  |
      | student1 | C1     | student         |

  @javascript @accessibility
  Scenario Outline: The grade overview report index page should be accessible
    Given I am logged in as "<user>"
    When I follow "Grades" in the user menu
    Then "<headingname>" "heading" should exist
    And the page should meet accessibility standards with "best-practice" extra tests

    Examples:
      | user      | headingname           |
      | student1  | Courses I am taking   |
      | teacher1  | Courses I am teaching |
