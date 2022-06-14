@mod @mod_assign
Feature: In an assignment, teachers can filter displayed submissions and see drafts
  In order to manage submissions more easily
  As a teacher
  I need to view submissions with draft status.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name            | assignsubmission_onlinetext_enabled | submissiondrafts |
      | assign   | C1     | Test assignment | 1                                   | 1                |
    And I am on the "Test assignment" Activity page logged in as student1
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | This submission is submitted |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And I log out

    And I am on the "Test assignment" Activity page logged in as student2
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | This submission is NOT submitted |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: View assignments with draft status on the view all submissions page
    Given I am on the "Test assignment" Activity page logged in as teacher1
    And I follow "View all submissions"
    When I set the field "Filter" to "Draft"
    Then I should see "Student 2"
    And I should not see "Student 1"
    And I should not see "Student 3"

  @javascript
  Scenario: View assignments with draft status in the grader
    Given I am on the "Test assignment" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    When I click on "[data-region=user-filters]" "css_element"
    And I set the field "filter" to "Draft"
    Then I should see "1 of 1"
    And I should see "No users selected"
    And I click on "[data-region=user-selector]" "css_element"
    And I type "Student"
    And I should see "Student 2"
    And I should not see "Student 1"
    And I should not see "Student 3"
