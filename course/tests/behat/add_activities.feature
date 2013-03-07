@core_course
Feature: Add activities to courses
  In order to provide tools for students learning
  As a teacher
  I need to add activites to a course

  @javascript
  Scenario: Add an activity to the course
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
    When I turn editing mode on
    And I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
      | Description | Test database description |
      | Required entries | 9 |
      | Comments | Yes |
    And I turn editing mode off
    Then I should not see "Adding a new"
    And I follow "Test name"
    And I follow "Edit settings"
    And the "Name" field should match "Test name" value
    And the "Required entries" field should match "9" value
    And the "Comments" field should match "Yes" value

  @javascript
  Scenario: Add an activity without the required fields
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
    When I turn editing mode on
    And I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
    Then I should see "Adding a new"
    And I should see "Required"
