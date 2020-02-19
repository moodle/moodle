@core @core_course @javascript
Feature: Display and choose from the available activities in course
  In order to add activities to a course
  As a teacher
  I should be enabled to choose from a list of available activities and also being able to read their summaries.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | Teacher | 1 | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course | C | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher | C | editingteacher |
    And I log in as "teacher"
    And I am on "Course" course homepage with editing mode on

  Scenario: The available activities are displayed to the teacher in the activity chooser
    Given I click on "Add an activity or resource" "button" in the "Topic 1" "section"
    Then I should see "Add an activity or resource" in the ".modal-title" "css_element"
    And I should see "Assignment" in the ".modal-body" "css_element"

  Scenario: The teacher can choose to add an activity from the activity items in the activity chooser
    Given I click on "Add an activity or resource" "button" in the "Topic 3" "section"
    When I click on "Add a new Assignment" "link" in the "Add an activity or resource" "dialogue"
    Then I should see "Adding a new Assignment"
    And I set the following fields to these values:
      | Assignment name | Test Assignment Topic 3 |
    And I press "Save and return to course"
    Then I should see "Test Assignment Topic 3" in the "Topic 3" "section"

  Scenario: The teacher can choose to add an activity from the activity summary in the activity chooser
    Given I click on "Add an activity or resource" "button" in the "Topic 1" "section"
    When I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    When I click on "Add a new Assignment" "link" in the "help" "core_course > Activity chooser screen"
    Then I should see "Adding a new Assignment"

  Scenario: Show summary
    Given I click on "Add an activity or resource" "button" in the "Topic 1" "section"
    When I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    Then I should see "Assignment" in the "help" "core_course > Activity chooser screen"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback."

  Scenario: Hide summary
    Given I click on "Add an activity or resource" "button" in the "Topic 1" "section"
    When I click on "Information about the Assignment activity" "button" in the "modules" "core_course > Activity chooser screen"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback." in the "help" "core_course > Activity chooser screen"
    And I should see "Back" in the "help" "core_course > Activity chooser screen"
    When I click on "Back" "button" in the "help" "core_course > Activity chooser screen"
    Then "modules" "core_course > Activity chooser screen" should exist
    And "help" "core_course > Activity chooser screen" should not exist
    And "Back" "button" should not exist in the "modules" "core_course > Activity chooser screen"
    And I should not see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback." in the "Add an activity or resource" "dialogue"

  # Currently stubbed out in MDL-67321 as further issues will add more tabs.
  Scenario: Navigate between module tabs
    Given I open the activity chooser
    And I should see "Activities" in the "Add an activity or resource" "dialogue"
    Then I should see "Forum" in the "default" "core_course > Activity chooser tab"
