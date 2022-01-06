@core @core_course
Feature: Add activities to courses
  In order to provide tools for students learning
  As a teacher
  I need to add activites to a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Add an activity to a course
    When I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
      | Description | Test database description |
      | ID number | TESTNAME |
      | Allow comments on entries | Yes |
    And I turn editing mode off
    Then I should not see "Adding a new"
    And I turn editing mode on
    And I open "Test name" actions menu
    And I click on "Edit settings" "link" in the "Test name" activity
    And I expand all fieldsets
    And the field "Name" matches value "Test name"
    And the field "ID number" matches value "TESTNAME"
    And the field "Allow comments on entries" matches value "Yes"

  @javascript
  Scenario: Add an activity supplying only the name
    When I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Test name"

  @javascript
  Scenario: Set activity description to required then add an activity supplying only the name
    Given I set the following administration settings values:
      | requiremodintro | Yes |
    When I am on "Course 1" course homepage
    And I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Required"
