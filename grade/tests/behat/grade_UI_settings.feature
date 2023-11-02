@core @core_grades
Feature: Site settings can be used to hide parts of the gradebook UI
  In order to hide UI elements
  As an admin
  I need to modify gradebook related system settings

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | s1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro |
      | assign | C1 | assign1 | Assignment1 | Assignment 1 intro |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "admin"
    And I turn editing mode on

  @javascript
  Scenario: Hide minimum grade
    When I click on "Edit  assign Assignment1" "link"
    And I should see "Minimum grade"
    Then I navigate to "Grades > General settings" in site administration
    And I set the field "Show minimum grade" to "0"
    And I press "Save changes"
    And I am on the "Course 1" "grades > Grader report > View" page
    And I click on "Edit  assign Assignment1" "link"
    And I should not see "Minimum grade"

  @javascript
  Scenario: Hide calculation icons
    And "Edit calculation for   Course total" "link" should exist
    When I navigate to "Grades > Report settings > Grader report" in site administration
    And I set the field "Show calculations" to "0"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    Then "Edit calculation for   Course total" "link" should not exist

  @javascript
  Scenario: Disable category overriding
    Given "Student 1 Course total" "field" should exist
    And I navigate to "Grades > Grade category settings" in site administration
    And I set the field "Allow category grades to be manually overridden" to "0"
    And I press "Save changes"
    When I am on the "Course 1" "grades > Grader report > View" page
    Then "Student 1 Course total" "field" should not exist
