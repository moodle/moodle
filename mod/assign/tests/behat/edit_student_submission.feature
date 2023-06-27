@mod @mod_assign @javascript
Feature: In an assignment, the administrator can edit students' submissions
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
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | submissiondrafts                    | 0                       |
      | assignsubmission_onlinetext_enabled | 1                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |

    And I am on the "Test assignment name" Activity page logged in as admin
    And I navigate to "View all submissions" in current page administration
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
