@mod @mod_qbassign @javascript
Feature: In an qbassignment, the administrator can edit students' submissions
  In order to edit a student's submissions
  As an administrator
  I need to grade multiple students on one page

  Scenario: Editing a student's submission
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity                            | qbassign                  |
      | course                              | C1                      |
      | name                                | Test qbassignment name    |
      | intro                               | Submit your online text |
      | submissiondrafts                    | 0                       |
      | qbassignsubmission_onlinetex_enabled | 1                       |
    And the following "mod_qbassign > submissions" exist:
      | qbassign                | user      | onlinetex                   |
      | Test qbassignment name  | student1  | I'm the student1 submission  |

    And I am on the "Test qbassignment name" Activity page logged in as admin
    And I follow "View all submissions"
    And I open the action menu in "Student 1" "table_row"
    And I choose "Edit submission" in the open action menu
    And I set the following fields to these values:
      | Online text | Have you seen the movie Chef? |
    And I press "Save changes"
    Then I should see "Have you seen the movie Chef?"
    And I open the action menu in "Student 1" "table_row"
    And I choose "Edit submission" in the open action menu
    And I set the following fields to these values:
      | Online text | I have seen the movie chef. |
    And I press "Save changes"
    Then I should see "I have seen the movie chef."
