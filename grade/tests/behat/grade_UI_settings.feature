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
      | student1 | Student | 1 | student1@asd.com | s1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro |
      | assign | C1 | assign1 | Assignment1 | Assignment 1 intro |
    And I log in as "admin"
    And I follow "Course 1"
    And I follow "Grades"
    And I turn editing mode on

  @javascript
  Scenario: Hide minimum grade
    When I click on "Edit  assign Assignment1" "link"
    And I should see "Minimum grade"
    Then I navigate to "General settings" node in "Site administration > Grades"
    And I click on "Show minimum grade" "checkbox"
    And I press "Save changes"
    And I follow "Home"
    And I follow "Course 1"
    And I follow "Grades"
    And I click on "Edit  assign Assignment1" "link"
    And I should not see "Minimum grade"

  @javascript
  Scenario: Hide calculation icons
    And "Edit calculation for   Course total" "link" should exist
    When I navigate to "Grader report" node in "Site administration > Grades > Report settings"
    And I click on "Show calculations" "checkbox"
    And I press "Save changes"
    And I follow "Home"
    And I follow "Course 1"
    And I follow "Grades"
    Then "Edit calculation for   Course total" "link" should not exist

  @javascript
  Scenario: Disable category overriding
    And ".r1 .course input[type='text']" "css_element" should exist
    Then I navigate to "Grade category settings" node in "Site administration > Grades"
    And I click on "Allow category grades to be manually overridden" "checkbox"
    And I press "Save changes"
    And I follow "Home"
    And I follow "Course 1"
    And I follow "Grades"
    And ".r0 .course input[type='text']" "css_element" should not exist
