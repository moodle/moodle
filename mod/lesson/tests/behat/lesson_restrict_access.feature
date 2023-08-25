@mod @mod_lesson
Feature: Lesson activity access can be restricted
  In order to restrict access to lesson activity
  As a teacher
  I need to set the lesson activity restriction

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group   |
      | student1 | G1      |
      | student2 | G2      |
    And the following "activities" exist:
      | activity   | name        | course | groupmode |
      | lesson     | Test lesson | C1     | 1         |

  @javascript
  Scenario Outline: Restrict lesson activity access by group
    # Set lesson activity access by group
    # Restrict lesson activity access by group as teacher
    Given I am on the "Test lesson" "lesson activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    # Set only Group 1 can access activity
    And I set the field "Group" in the "Restrict access" "fieldset" to "Group 1"
    And I click on "Displayed if student doesn't meet this condition" "button"
    And I press "Save and return to course"
    # Confirm that student1 can see lesson but student2 can't
    When I am on the "Course 1" course page logged in as <user>
    Then I <visibility> "Test lesson" in the "region-main" "region"

    Examples:
      | user     | visibility     |
      | student1 | should see     |
      | student2 | should not see |
