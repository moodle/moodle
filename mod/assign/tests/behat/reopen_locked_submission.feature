@mod @mod_assign
Feature: Submissions are unlocked when a new attempt is given
  In order to allow students to reattempt a locked submission
  As a teacher
  I need to use quick grading to grant a new submission

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: A locked submission should unlock when a new attempt is automatically given.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | Attempts reopened | Automatically until pass |
      | Grade to pass | 50 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Edit" "link" in the "Student 1" "table_row"
    And I follow "Prevent submission changes"
    And I should see "Submission changes not allowed"
    And I click on "Quick grading" "checkbox"
    And I set the field "User grade" to "49.0"
    And I press "Save all quick grading changes"
    And I should see "The grade changes were saved"
    And I press "Continue"
    Then I should see "Reopened"
    And I should not see "Submission changes not allowed"

  @javascript
  Scenario: A locked submission should unlock when a new attempt is manually given.
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | Attempts reopened | Manually |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Edit" "link" in the "Student 1" "table_row"
    And I follow "Prevent submission changes"
    And I should see "Submission changes not allowed"
    And I click on "Edit" "link" in the "Student 1" "table_row"
    And I follow "Allow another attempt"
    Then I should see "Reopened"
    And I should not see "Submission changes not allowed"
