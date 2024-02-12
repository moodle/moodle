@core @core_course @javascript
Feature: Use the activity chooser to insert activities anywhere in a section
  In order to add activities to a course
  As a teacher
  I should be able to add an activity anywhere in a section.

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname  | email               |
      | teacher   | Teacher   | 1         | teacher@example.com |
    And the following "course" exists:
      | fullname    | Course |
      | shortname   | C      |
      | format      | topics |
      | numsections | 1      |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher   | C       | editingteacher  |
    And the following "activities" exist:
      | activity  | course | idnumber | intro | name        | section  |
      | page      | C      | p1       | x     | Test Page   | 1        |
      | forum     | C      | f1       | x     | Test Forum  | 1        |
      | label     | C      | l1       | x     | Test Label  | 1        |
    And I log in as "teacher"
    And I am on "Course" course homepage with editing mode on

  Scenario: The activity chooser icon is hidden by default and be made visible on hover
    Given I hover ".navbar-brand" "css_element"
    And "Insert an activity or resource before 'Test Forum'" "button" should not be visible
    When I hover "Insert an activity or resource before 'Test Forum'" "button"
    Then "Insert an activity or resource before 'Test Forum'" "button" should be visible

  Scenario: The activity chooser can be used to insert modules before existing modules
    Given I hover "Insert an activity or resource before 'Test Forum'" "button"
    And I press "Insert an activity or resource before 'Test Forum'"
    And I should see "Add an activity or resource" in the ".modal-title" "css_element"
    When I click on "Add a new Assignment" "link" in the "Add an activity or resource" "dialogue"
    And I set the following fields to these values:
      | Assignment name | Test Assignment |
    And I press "Save and return to course"
    And I should see "Test Assignment" in the "New section" "section"
    # Ensure the new assignment is in the middle of the two existing modules.
    Then "Test Page" "text" should appear before "Test Assignment" "text"
    And "Test Assignment" "text" should appear before "Test Forum" "text"
