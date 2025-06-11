@mod @mod_assign
Feature: In an assignment, teachers can use table preferences.
  In order to improve grading process
  As a teacher
  I need to be able to filter students by first and last name.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity                            | assign             |
      | course                              | C1                 |
      | name                                | Test assignment    |
      | assignsubmission_onlinetext_enabled | 1                  |
    And I log out
    And I log in as "student1"
    And I am on the "Test assignment" Activity page
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | This is a submission for Student One |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And I log out
    And I log in as "student2"
    And I am on the "Test assignment" Activity page
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | This is a submission for Student Two |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"

  @javascript
  Scenario: As a teacher I can filter student submissions on the View all submission page
    When I log in as "teacher1"
    And I am on the "Test assignment" Activity page
    And I navigate to "Submissions" in current page administration
    And I click on "Filter by name" "combobox"
    And I select "T" in the "Last name" "core_course > initials bar"
    And I press "Apply"
    And I change window size to "large"
    And I click on "Grade actions" "actionmenu" in the "Student Two" "table_row"
    And I choose "Grade" in the open action menu
    And I change window size to "medium"
    And I should see "This is a submission for Student Two"
    And I should see "1 of 1"
    And I follow "Reset table preferences"
    Then I should see "This is a submission for Student Two"
    And I should see "2 of 2"
    And I log out
