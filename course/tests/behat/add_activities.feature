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
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

  @javascript
  Scenario: Add an activity to a course
    When I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
      | Description | Test database description |
      | Entries required for completion | 9 |
      | Allow comments on entries | Yes |
    And I turn editing mode off
    Then I should not see "Adding a new"
    And I follow "Test name"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I expand all fieldsets
    And the field "Name" matches value "Test name"
    And the field "Entries required for completion" matches value "9"
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
    When I am on site homepage
    And I follow "Course 1"
    And I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Required"

  Scenario: Add an activity to a course with Javascript disabled
    Then I should see "Add a resource to section 'Topic 1'"
    And I should see "Add an activity to section 'Topic 1'"
    And I should see "Add a resource to section 'Topic 2'"
    And I should see "Add an activity to section 'Topic 2'"
    And I should see "Add a resource to section 'Topic 3'"
    And I should see "Add an activity to section 'Topic 3'"
    And I add a "Label" to section "2"
    And I should see "Adding a new Label to Topic 2"
    And I set the following fields to these values:
      | Label text | I'm a label |
    And I press "Save and return to course"
    And I add a "Database" to section "3"
    And I should see "Adding a new Database to Topic 3"
    And I set the following fields to these values:
      | Name | Test database name |
      | Description | Test database description |
    And I press "Save and return to course"
    And I should not see "Adding a new"
    And I should see "Test database name"
    And I should see "I'm a label"
