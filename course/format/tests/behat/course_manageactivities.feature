@core @core_courseformat @show_editor
Feature: Verify edit utils availability
  In order to edit the course activities
  As a student with capability 'moodle/course:manageactivities'
  I need to be able to use the edit utils.

  Background:
    Given the following "course" exists:
      | fullname     | Course 1 |
      | shortname    | C1       |
      | category     | 0        |
      | numsections  | 3        |
      | initsections | 1        |
    And the following "activities" exist:
      | activity | name              | intro                       | course | idnumber | section |
      | assign   | Activity sample 1 | Test assignment description | C1     | sample1  | 1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | author1  | Author    | 1        | author1@example.com  |
    And the following "roles" exist:
      | shortname | name   | archetype |
      | author    | Author | student   |
    And the following "permission overrides" exist:
      | capability                     | permission | role   | contextlevel | reference |
      | moodle/course:manageactivities | Allow      | author | Course       | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | author1  | C1     | author         |
      | student1 | C1     | student        |

  @javascript
  Scenario: Edit tools should be available to teachers.
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I turn editing mode on
    Then I should see "Add an activity or resource"
    And I open "Activity sample 1" actions menu
    And I should see "Edit settings"
    And ".section_action_menu" "css_element" should exist in the "Section 1" "section"
    And I click on ".section_action_menu" "css_element" in the "Section 1" "section"
    And I should see "Edit settings"

  @javascript
  Scenario: Edit mode should not be available to students.
    Given I log in as "student1"
    When I am on "Course 1" course homepage
    Then I should not see "Edit mode"

  @javascript
  Scenario: Edit tools should be available to students with manageactivities capability but not allowed to add sections without course:update
    Given I log in as "author1"
    When I am on "Course 1" course homepage
    And I turn editing mode on
    Then I should see "Add an activity or resource"
    But I should not see "Add section"
    And I open "Activity sample 1" actions menu
    And I should see "Edit settings"
    And I open section "1" edit menu
    And I should not see "Edit settings"
    And I should see "View"

  @javascript
  Scenario: Section adding should be available to students if they also have the capability 'moodle/course:update'.
    Given the following "permission overrides" exist:
      | capability           | permission | role   | contextlevel | reference |
      | moodle/course:update | Allow      | author | Course       | C1        |
    And I log in as "author1"
    When I am on "Course 1" course homepage
    And I turn editing mode on
    Then I should see "Add an activity or resource"
    And I should see "Add section"
    And I open "Activity sample 1" actions menu
    And I should see "Edit settings"
    And ".section_action_menu" "css_element" should exist in the "Section 1" "section"
    And I click on ".section_action_menu" "css_element" in the "Section 1" "section"
    And I should see "Edit settings"
