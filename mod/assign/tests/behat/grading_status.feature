@mod @mod_assign
Feature: View the grading status of an assignment
  In order to test the grading status for assignments is displaying correctly
  As a student
  I need to view my grading status

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
  Scenario: View the grading status for an assignment with marking workflow enabled
    # Add the assignment.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Online text | 1 |
      | Use marking workflow | Yes |
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
    And I should see "Not marked" in the "Student 1" "table_row"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I set the field "Marking workflow state" to "In review"
    And I set the field "Feedback comments" to "Great job! Lol, not really."
    And I set the field "Notify students" to "0"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "In review" in the "Student 1" "table_row"
    And I log out
    # View the grading status as a student.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "In review" in the "Grading status" "table_row"
    And I should not see "Great job! Lol, not really."
    And I log out
    # Mark the submission again but set the marking workflow to 'Released'.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "In review" in the "Student 1" "table_row"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Released" in the "Student 1" "table_row"
    And I log out
    # View the grading status as a student.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "Released" in the "Grading status" "table_row"
    And I should see "Great job! Lol, not really."
    And I log out
    # Now, change the status from 'Released' to 'In marking' (this will remove the grade from the gradebook).
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Released" in the "Student 1" "table_row"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Notify students" to "0"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "In marking" in the "Student 1" "table_row"
    # The grade should also remain displayed as it's stored in the assign DB tables, but the final grade should be empty.
    And "Student 1" row "Grade" column of "generaltable" table should contain "50.00"
    And "Student 1" row "Final grade" column of "generaltable" table should contain "-"
    And I log out

  @javascript
  Scenario: View the grading status for an assignment with marking workflow disabled
    # Add the assignment.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Online text | 1 |
    And I log out
    # Add a submission.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should not see "Feedback"
    And I should see "Not graded" in the "Grading status" "table_row"
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
    And I should not see "Graded" in the "Student 1" "table_row"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I set the field "Feedback comments" to "Great job! Lol, not really."
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Graded" in the "Student 1" "table_row"
    And I log out
    # View the grading status as a student.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "Graded" in the "Grading status" "table_row"
    And I should see "Great job! Lol, not really."
    And I log out
    # Student makes a subsequent submission.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Edit submission"
    And I set the following fields to these values:
      | Online text | I'm the student's second submission |
    And I press "Save changes"
    And I log out
    # Teacher marks the submission again after noticing the 'Graded - follow up submission received'.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Graded - follow up submission received" in the "Student 1" "table_row"
    And I wait "10" seconds
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "99.99"
    And I set the field "Feedback comments" to "Even better job! Really."
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Graded" in the "Student 1" "table_row"
    And I log out
    # View the grading status as a student again.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "Graded" in the "Grading status" "table_row"
    And I should see "Even better job! Really."
    And I log out
