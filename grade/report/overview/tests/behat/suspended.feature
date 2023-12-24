@gradereport @gradereport_overview
Feature: Grade overview report should be hidden from suspended enrolments
  While viewing the grade overview report
  As a student
  I should only see courses I am active in

  Background:
    Given the following "courses" exist:
      | fullname         | shortname |
      | Active course    | C1        |
      | Suspended course | C2        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    | status |
      | student1 | C1     | student | 0      |
      | student1 | C2     | student | 1      |

  Scenario: Students should not see grades for courses with suspended enrolments
    Given I am on the "Active course" "grades > Overview report > View" page logged in as "student1"
    Then I should not see "Suspended course" in the "overview-grade" "table"
    And I should see "Active course" in the "overview-grade" "table"

  Scenario: Admins should see courses with suspended enrolments
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Student 1"
    When I click on "Grades overview" "link"
    Then I should see "Suspended course" in the "overview-grade" "table"
    And I should see "Active course" in the "overview-grade" "table"
