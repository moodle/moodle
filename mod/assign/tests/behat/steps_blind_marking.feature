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
    # Add the assignment.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Online text | 1 |
      | File submissions | 0 |
      | Use marking workflow | Yes |
      | Blind marking | Yes |
    And I log out
    # Add a submission.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test assignment name"
    Then I should not see "Feedback"
    And I should see "Not marked" in the "Grading status" "table_row"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student's first submission |
    And I press "Save changes"
    And I log out
    # Mark the submission.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Not marked" in the "I'm the student's first submission" "table_row"
    And I click on "Grade" "link" in the "I'm the student's first submission" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I set the field "Marking workflow state" to "In review"
    And I set the field "Feedback comments" to "Great job! Lol, not really."
    And I set the field "Notify students" to "0"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "In review" in the "I'm the student's first submission" "table_row"

  @javascript
  Scenario: Student identities are revealed after releasing the grades.
    When I click on "Grade" "link" in the "I'm the student's first submission" "table_row"
    And I set the field "Marking workflow state" to "Ready for release"
    And I set the field "Notify students" to "0"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Ready for release" in the "I'm the student's first submission" "table_row"
    And I click on "Grade" "link" in the "I'm the student's first submission" "table_row"
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Released" in the "I'm the student's first submission" "table_row"
    And I set the field "Grading action" to "Reveal student identities"
    And I press "Continue"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to "User report" in the course gradebook
    Then I should see "50"
    And I should see "Great job! Lol, not really."

  @javascript
  Scenario: Student identities are revealed before releasing the grades.
    When I click on "Grade" "link" in the "I'm the student's first submission" "table_row"
    And I set the field "Marking workflow state" to "Ready for release"
    And I set the field "Notify students" to "0"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Ready for release" in the "I'm the student's first submission" "table_row"
    And I set the field "Grading action" to "Reveal student identities"
    And I press "Continue"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Released" in the "Student 1" "table_row"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I navigate to "User report" in the course gradebook
    Then I should see "50"
    And I should see "Great job! Lol, not really."

  @javascript
  Scenario: Submissions table visible with overrides and blind marking
    When I follow "Test assignment name"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user      | Student |
      | id_duedate_enabled | 1 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2020 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "In review" in the "I'm the student's first submission" "table_row"
