@core @core_courseformat @show_editor @javascript
Feature: Bulk activity and section selection.
  In order to edit the course activities
  As a teacher with capability 'moodle/course:manageactivities'
  I need to be able to bulk select activities or sections.

  Background:
    Given the following "course" exists:
      | fullname     | Course 1 |
      | shortname    | C1       |
      | category     | 0        |
      | numsections  | 4        |
      | initsections | 1        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
      | assign   | Activity sample 2 | Test assignment description | C1     | sample2  | 1       |
      | assign   | Activity sample 3 | Test assignment description | C1     | sample3  | 2       |
      | assign   | Activity sample 4 | Test assignment description | C1     | sample4  | 2       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I turn editing mode on

  Scenario: Enable and disable bulk editing
    When I click on "Bulk actions" "button"
    Then I should see "0 selected" in the "sticky-footer" "region"
    And the focused element is "Select section Section 1" "checkbox"
    And I click on "Close bulk actions" "button" in the "sticky-footer" "region"
    And "sticky-footer" "region" should not be visible
    And the focused element is "Bulk actions" "button"

  Scenario: Selecting activities disable section selection
    Given I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"
    When I click on "Select activity Activity sample 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    Then the "Select section Section 1" "checkbox" should be disabled

  Scenario: Selecting sections disable activity selection
    Given I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"
    When I click on "Select section Section 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    Then the "Select activity Activity sample 1" "checkbox" should be disabled

  Scenario: Disable bulk resets the selection
    Given I click on "Bulk actions" "button"
    And I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Select activity Activity sample 2" "checkbox"
    And I should see "2 selected" in the "sticky-footer" "region"
    And the field "Activity sample 1" matches value "1"
    And the field "Activity sample 2" matches value "1"
    And the field "Activity sample 3" matches value ""
    And the field "Activity sample 4" matches value ""
    When I click on "Close bulk actions" "button" in the "sticky-footer" "region"
    And I click on "Bulk actions" "button"
    Then I should see "0 selected" in the "sticky-footer" "region"
    And the field "Activity sample 1" matches value ""
    And the field "Activity sample 2" matches value ""
    And the field "Activity sample 3" matches value ""
    And the field "Activity sample 4" matches value ""

  Scenario: Select all is disabled until an activity is selected
    Given I click on "Bulk actions" "button"
    And the "Select all" "checkbox" should be disabled
    When I click on "Select activity Activity sample 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    Then the "Select all" "checkbox" should be enabled

  Scenario: Select all is disabled until a section is selected
    Given I click on "Bulk actions" "button"
    And the "Select all" "checkbox" should be disabled
    When I click on "Select section Section 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    Then the "Select all" "checkbox" should be enabled

  Scenario: Select all when an activity is selected will select all activities
    Given I click on "Bulk actions" "button"
    And I click on "Select activity Activity sample 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    And the field "Activity sample 1" matches value "1"
    And the field "Activity sample 2" matches value ""
    And the field "Activity sample 3" matches value ""
    And the field "Activity sample 4" matches value ""
    And the "Select all" "checkbox" should be enabled
    When I click on "Select all" "checkbox" in the "sticky-footer" "region"
    Then I should see "4 selected" in the "sticky-footer" "region"
    And the field "Activity sample 1" matches value "1"
    And the field "Activity sample 2" matches value "1"
    And the field "Activity sample 3" matches value "1"
    And the field "Activity sample 4" matches value "1"

  Scenario: Select all when a section is selected will select all sections
    Given I click on "Bulk actions" "button"
    And I click on "Select section Section 1" "checkbox"
    And I should see "1 selected" in the "sticky-footer" "region"
    And the field "Select section Section 1" matches value "1"
    And the field "Select section Section 2" matches value ""
    And the field "Select section Section 3" matches value ""
    And the field "Select section Section 4" matches value ""
    And the "Select all" "checkbox" should be enabled
    When I click on "Select all" "checkbox" in the "sticky-footer" "region"
    Then I should see "4 selected" in the "sticky-footer" "region"
    And the field "Select section Section 1" matches value "1"
    And the field "Select section Section 2" matches value "1"
    And the field "Select section Section 3" matches value "1"
    And the field "Select section Section 4" matches value "1"

  Scenario: Click on a select all with all sections selected unselects all sections
    Given I click on "Bulk actions" "button"
    And I click on "Select section Section 1" "checkbox"
    And I click on "Select section Section 2" "checkbox"
    And I click on "Select section Section 3" "checkbox"
    And I click on "Select section Section 4" "checkbox"
    And I should see "4 selected" in the "sticky-footer" "region"
    And the "Select all" "checkbox" should be enabled
    When I click on "Select all" "checkbox" in the "sticky-footer" "region"
    Then I should see "0 selected" in the "sticky-footer" "region"
    And the focused element is "Select section Section 1" "checkbox"
    And the field "Select section Section 1" matches value ""
    And the field "Select section Section 2" matches value ""
    And the field "Select section Section 3" matches value ""
    And the field "Select section Section 4" matches value ""

  Scenario: Click on a select all with all activity selected unselects all activities
    Given I click on "Bulk actions" "button"
    And I click on "Select activity Activity sample 1" "checkbox"
    And I click on "Select activity Activity sample 2" "checkbox"
    And I click on "Select activity Activity sample 3" "checkbox"
    And I click on "Select activity Activity sample 4" "checkbox"
    And I should see "4 selected" in the "sticky-footer" "region"
    And the "Select all" "checkbox" should be enabled
    When I click on "Select all" "checkbox" in the "sticky-footer" "region"
    Then I should see "0 selected" in the "sticky-footer" "region"
    And the focused element is "Select section Section 1" "checkbox"
    And the field "Activity sample 1" matches value ""
    And the field "Activity sample 2" matches value ""
    And the field "Activity sample 3" matches value ""
    And the field "Activity sample 4" matches value ""

  Scenario: Click an activity name in bulk mode select and unselects the activity
    Given I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"
    When I click on "Activity sample 1" "link" in the "Section 1" "section"
    And the field "Activity sample 1" matches value "1"
    And the field "Activity sample 2" matches value ""
    And the field "Activity sample 3" matches value ""
    And the field "Activity sample 4" matches value ""
    And I click on "Activity sample 2" "link" in the "Section 1" "section"
    And the field "Activity sample 1" matches value "1"
    And the field "Activity sample 2" matches value "1"
    And the field "Activity sample 3" matches value ""
    And the field "Activity sample 4" matches value ""
    And I should see "2 selected" in the "sticky-footer" "region"
    Then I click on "Activity sample 1" "link" in the "Section 1" "section"
    And I should see "1 selected" in the "sticky-footer" "region"
    And the field "Activity sample 1" matches value ""
    And the field "Activity sample 2" matches value "1"
    And the field "Activity sample 3" matches value ""
    And the field "Activity sample 4" matches value ""

  Scenario: Select a range of activities using shift
    Given I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"
    When I click on "Activity sample 1" "link" in the "Section 1" "section"
    And the field "Activity sample 1" matches value "1"
    And the field "Activity sample 2" matches value ""
    And the field "Activity sample 3" matches value ""
    And the field "Activity sample 4" matches value ""
    And I shift click on "Activity sample 3" "link" in the "Section 2" "section"
    Then I should see "3 selected" in the "sticky-footer" "region"
    And the field "Activity sample 1" matches value "1"
    And the field "Activity sample 2" matches value "1"
    And the field "Activity sample 3" matches value "1"
    And the field "Activity sample 4" matches value ""

  Scenario: Select all activities in a section using alt
    Given I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"
    When I alt click on "Activity sample 3" "link" in the "Section 2" "section"
    Then I should see "2 selected" in the "sticky-footer" "region"
    And the field "Activity sample 1" matches value ""
    And the field "Activity sample 2" matches value ""
    And the field "Activity sample 3" matches value "1"
    And the field "Activity sample 4" matches value "1"

  Scenario: Select a range of sections using shift
    Given I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"
    When I click on "Select section Section 1" "checkbox"
    And the field "Select section Section 1" matches value "1"
    And the field "Select section Section 2" matches value ""
    And the field "Select section Section 3" matches value ""
    And the field "Select section Section 4" matches value ""
    And I shift click on "Select section Section 3" "checkbox" in the "page" "region"
    Then I should see "3 selected" in the "sticky-footer" "region"
    And the field "Select section Section 1" matches value "1"
    And the field "Select section Section 2" matches value "1"
    And the field "Select section Section 3" matches value "1"
    And the field "Select section Section 4" matches value ""

  Scenario: Select all section with alt click
    Given I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"
    And the field "Select section Section 1" matches value ""
    And the field "Select section Section 2" matches value ""
    And the field "Select section Section 3" matches value ""
    And the field "Select section Section 4" matches value ""
    When I alt click on "Select section Section 3" "checkbox" in the "page" "region"
    And I should see "4 selected" in the "sticky-footer" "region"
    And the field "Select section Section 1" matches value "1"
    And the field "Select section Section 2" matches value "1"
    And the field "Select section Section 3" matches value "1"
    And the field "Select section Section 4" matches value "1"

  Scenario: Bulk section selection is available also in one section per page
    Given I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Course layout" to "Show one section per page"
    And I click on "Save and display" "button"
    And I click on "Bulk actions" "button"
    And I should see "0 selected" in the "sticky-footer" "region"
    When I click on "Select section Section 1" "checkbox"
    And I click on "Select section Section 2" "checkbox"
    Then I should see "2 selected" in the "sticky-footer" "region"

  Scenario: Bulk selection in small devices
    Given I change the viewport size to "mobile"
    And I close block drawer if open
    When I click on "Bulk actions" "button"
    Then I should not see "0 selected" in the "sticky-footer" "region"
    And I should not see "Select all" in the "sticky-footer" "region"
    And I should not see "Availability" in the "sticky-footer" "region"
    And I should not see "Duplicate" in the "sticky-footer" "region"
    And I should not see "Move" in the "sticky-footer" "region"
    And I should not see "Delete" in the "sticky-footer" "region"
    And I click on "Close bulk actions" "button" in the "sticky-footer" "region"
    And "sticky-footer" "region" should not be visible
