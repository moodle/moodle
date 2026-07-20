@mod @mod_assign
Feature: In an assignment, the administrator can edit students' submissions
  In order to edit a student's submissions
  As an administrator or teacher with the right permissions
  I need to grade multiple students on one page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | submissiondrafts                    | 0                       |
      | assignsubmission_onlinetext_enabled | 1                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |

  @javascript
  Scenario: Admin can edit a submission
    Given I am on the "Test assignment name" Activity page logged in as admin
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    And I open the action menu in "Student 1" "table_row"
    And I change window size to "medium"
    And I choose "Edit submission" in the open action menu
    When I set the following fields to these values:
      | Online text | Have you seen the movie Chef? |
    And I press "Save changes"
    Then I should see "Have you seen the movie Chef?"
    And I open the action menu in "Student 1" "table_row"
    And I choose "Edit submission" in the open action menu
    And I set the following fields to these values:
      | Online text | I have seen the movie chef. |
    And I press "Save changes"
    And I should see "I have seen the movie chef."

  @javascript
  Scenario: Admin can edit a submission after cutoff date but only if user is allowed to via extension or override
    # Make an assignment with a cutoff date in the past (5 days ago), and two more students.
    Given the following "activity" exists:
      | activity                            | assign           |
      | course                              | C1               |
      | name                                | Test2 assignment |
      | submissiondrafts                    | 0                |
      | assignsubmission_onlinetext_enabled | 1                |
      | cutoffdate                          | ## 2025-01-10 ## |
    And the time is frozen at "2025-01-15"
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student2 | C1     | student |
      | student3 | C1     | student |
    # One of students has an extension, the other one has a user override to turn off the cutoff date.
    And the following "mod_assign > extensions" exist:
      | assign           | user     | extensionduedate |
      | Test2 assignment | student2 | ## 2025-01-20 ## |
    And the following "mod_assign > user overrides" exist:
      | assignment       | user     | cutoffdate |
      | Test2 assignment | student3 | 0          |
    And I am on the "Test2 assignment" Activity page logged in as admin
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    When I open the action menu in "Student 1" "table_row"
    Then I should not see "Edit submission"
    # Click on the heading at the top, just to close the actions menu. The open menu covers
    # the next one so it gets in the way unless we close it first.
    And I click on ".page-header-headings" "css_element"
    And I open the action menu in "Student 2" "table_row"
    And I should see "Edit submission"
    And I choose "Edit submission" in the open action menu
    And I set the following fields to these values:
      | Online text | Have you seen the movie The Magic Faraway Tree? |
    And I press "Save changes"
    And I should see "Have you seen the movie The Magic Faraway Tree?" in the "Student 2" "table_row"
    And I open the action menu in "Student 3" "table_row"
    And I should see "Edit submission"
    And I choose "Edit submission" in the open action menu
    And I set the following fields to these values:
      | Online text | I have seen the movie The Magic Faraway Tree. |
    And I press "Save changes"
    And I should see "I have seen the movie The Magic Faraway Tree." in the "Student 3" "table_row"

  @javascript
  Scenario: Teacher can edit a submission when granted the necessary permissions
    Given the following "permission overrides" exist:
      | capability                     | permission | role           | contextlevel | reference |
      | mod/assign:editothersubmission | Allow      | editingteacher | Course       | C1        |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I open the action menu in "Student 1" "table_row"
    And I choose "Edit submission" in the open action menu
    When I set the following fields to these values:
      | Online text | Have you seen the movie Chef? |
    And I press "Save changes"
    Then I should see "Have you seen the movie Chef?"
    And I open the action menu in "Student 1" "table_row"
    And I choose "Edit submission" in the open action menu
    And I set the following fields to these values:
      | Online text | I have seen the movie chef. |
    And I press "Save changes"
    And I should see "I have seen the movie chef."

  Scenario: Teacher cannot edit a submission
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    When I open the action menu in "Student 1" "table_row"
    Then I should not see "Edit Submission"
