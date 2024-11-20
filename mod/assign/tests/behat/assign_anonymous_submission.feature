@mod @mod_assign
Feature: Teacher can enable anonymous submissions for an assignment
  In order to make an anonymous submission to an assignment
  As a teacher
  I should be able to enable anonymous submissions

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name     | assignsubmission_onlinetext_enabled | blindmarking     |
      | assign   | C1     | Assign 1 | 1                                   | 1                |

  @javascript
  Scenario: Teacher can enable anonymous submissions
    # Submit an assignment as student1
    Given the following "mod_assign > submissions" exist:
      | assign   | user      | onlinetext                          |
      | Assign 1 | student1  | I'm the student's first submission  |
    When I am on the "Assign 1" "assign activity editing" page logged in as teacher1
    # Confirm that anonymous submissions can't be changed to no anymore
    Then "blindmarking" "select" should not exist
    And I press "Cancel"
    And I am on the "Assign 1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    # Confirm that Participant [n] is displayed instead of Student One - student name is hidden
    And I should not see "Student One" in the "Participant" "table_row"
    And I click on "Grade actions" "actionmenu" in the "Participant" "table_row"
    And I choose "Grade" in the open action menu
    And I set the field "Grade out of 100" to "70"
    And I press "Save changes"
    And I am on the "Assign 1" "assign activity" page
    And I follow "Reveal student identities"
    And I should see "Are you sure you want to reveal student identities for this assignment? This operation cannot be undone. Once the student identities have been revealed, the marks will be released to the gradebook."
    And I press "Continue"
    # Confirm that student identity is no longer hidden and grade is retained
    And I should not see "Participant" in the "Student One" "table_row"
    And I should see "70.00" in the "Student One" "table_row"
