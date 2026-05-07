@enrol @enrol_self
Feature: Self-enrolment cohort restriction
  In order to prevent unauthorized access
  As a admin
  I need to restrict self-enrolment to cohort members

  Background:
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "cohorts" exist:
      | name     | idnumber | contextid |
      | Cohort A | CH1      | 1         |
    And the following "cohort members" exist:
      | user     | cohort |
      | student1 | CH1    |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And I log in as "admin"
    And I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Test student enrolment |
      | Only cohort members  | Cohort A               |

  Scenario: Self enrolment as cohort member
    Given I am on the "C1" "Course" page logged in as "student1"
    When I press "Enrol me"
    Then I should see "You are enrolled in the course."

  Scenario: Self enrolment as non cohort member
    Given I am on the "C1" "Course" page logged in as "student2"
    Then I should see "Only members of cohort 'Cohort A' can self-enrol."
