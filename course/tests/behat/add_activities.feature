@core @core_course
Feature: Add activities to courses
  In order to provide tools for students learning
  As a teacher
  I need to add activites to a course

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exists:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "admin"
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
    And I follow "Edit settings"
    And I expand all fieldsets
    And the "Name" field should match "Test name" value
    And the "Entries required for completion" field should match "9" value
    And the "Allow comments on entries" field should match "Yes" value

  @javascript
  Scenario: Add an activity without the required fields
    When I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Adding a new"
    And I should see "Required"

  Scenario: Add an activity to a course with Javascript disabled
    Then I should see "Add a resource to section 'Topic 1'"
    And I should see "Add an activity to section 'Topic 1'"
    And I should see "Add a resource to section 'Topic 2'"
    And I should see "Add an activity to section 'Topic 2'"
    And I should see "Add a resource to section 'Topic 3'"
    And I should see "Add an activity to section 'Topic 3'"
    And I add a "Label" to section "2"
    And I should see "Adding a new Label to Topic 2"
    And I fill the moodle form with:
      | Label text | I'm a label |
    And I press "Save and return to course"
    And I add a "Database" to section "3"
    And I should see "Adding a new Database to Topic 3"
    And I fill the moodle form with:
      | Name | Test database name |
      | Description | Test database description |
    And I press "Save and return to course"
    And I should not see "Adding a new"
    And I should see "Test database name"
    And I should see "I'm a label"
