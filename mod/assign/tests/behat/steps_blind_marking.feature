@mod @mod_assign
Feature: Assignments correctly add feedback to the grade report when workflow and blind marking are enabled.
  In order to give students feedback when blind marking
  As a teacher
  I should be able to reveal student identities at any time and have my feedback show
  to the student in the gradebook when the grades are in a released state.

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
    And the following "activity" exists:
      | activity                            | assign                      |
      | course                              | C1                          |
      | name                                | Test assignment name        |
      | submissiondrafts                    | 0                           |
      | assignsubmission_onlinetext_enabled | 1                           |
      | assignsubmission_file_enabled       | 0                           |
      | assignfeedback_comments_enabled     | 1                           |
      | teamsubmission                      | 1                           |
      | markingworkflow                     | 1                           |
      | blindmarking                        | 1                           |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |

    # Mark the submission.
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I should see "Not marked" in the "I'm the student's first submission" "table_row"
    And I click on "Grade" "link" in the "I'm the student's first submission" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I set the field "Marking workflow state" to "In review"
    And I set the field "Feedback comments" to "Great job! Lol, not really."
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    And I should see "In review" in the "I'm the student's first submission" "table_row"

  @javascript
  Scenario: Student identities are revealed after releasing the grades.
    When I click on "Grade" "link" in the "I'm the student's first submission" "table_row"
    And I set the field "Marking workflow state" to "Ready for release"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    And I should see "Ready for release" in the "I'm the student's first submission" "table_row"
    And I click on "Grade" "link" in the "I'm the student's first submission" "table_row"
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    And I follow "View all submissions"
    And I should see "Released" in the "I'm the student's first submission" "table_row"
    And I set the field "Grading action" to "Reveal student identities"
    And I press "Continue"
    And I log out

    And I am on the "C1" Course page logged in as student1
    And I navigate to "User report" in the course gradebook
    Then I should see "50"
    And I should see "Great job! Lol, not really."

  @javascript
  Scenario: Student identities are revealed before releasing the grades.
    When I click on "Grade" "link" in the "I'm the student's first submission" "table_row"
    And I set the field "Marking workflow state" to "Ready for release"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    And I should see "Ready for release" in the "I'm the student's first submission" "table_row"
    And I set the field "Grading action" to "Reveal student identities"
    And I press "Continue"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    And I follow "View all submissions"
    And I should see "Released" in the "Student 1" "table_row"
    And I log out

    And I am on the "C1" Course page logged in as student1
    And I navigate to "User report" in the course gradebook
    Then I should see "50"
    And I should see "Great job! Lol, not really."

  @javascript
  Scenario: Submissions table visible with overrides and blind marking
    When I am on the "Test assignment name" "assign activity" page
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user | Student              |
      | Due date      | ##2030-01-01 08:00## |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I am on the "Test assignment name" "assign activity" page
    And I follow "View all submissions"
    And I should see "In review" in the "I'm the student's first submission" "table_row"
