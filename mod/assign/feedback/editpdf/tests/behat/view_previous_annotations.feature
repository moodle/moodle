@mod @mod_assign @assignfeedback @assignfeedback_editpdf @_file_upload
Feature: In an assignment, teacher can view the feedback for a previous attempt.
  In order to see the history of attempts
  As a teacher
  I need to see the previous annotations.

  @javascript
  Scenario: Submit a PDF file as a student and annotate the PDF as a teacher, allowing another attempt
    Given ghostscript is installed
    And the following "courses" exist:
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your PDF file |
      | assignsubmission_file_enabled | 1 |
      | Maximum number of uploaded files | 2 |
      | Attempts reopened | Manually |
      | Maximum attempts | Unlimited |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I upload "mod/assign/feedback/editpdf/tests/fixtures/submission.pdf" file to "File submissions" filemanager
    And I upload "mod/assign/feedback/editpdf/tests/fixtures/testgs.pdf" file to "File submissions" filemanager
    And I press "Save changes"
    And I should see "Submitted for grading"
    And I should see "submission.pdf"
    And I should see "Not graded"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Submitted for grading" "table_row"
    And I should see "Page 1 of 3"
    And I click on ".navigate-next-button" "css_element"
    And I should see "Page 2 of 3"
    And I click on ".stampbutton" "css_element"
    And I draw on the pdf
    And I wait until the page is ready
    And I set the field "Allow another attempt" to "Yes"
    And I press "Save changes"
    And I wait until the page is ready
    And I should see "The changes to the grade and feedback were saved"
    And I follow "View a different attempt"
    And I click on "Attempt 1" "radio" in the "View a different attempt" "dialogue"
    And I press "View"
    And I wait until the page is ready
    And I should see "You are editing the feedback for a previous attempt. This is attempt 1 out of 2."
    And I should see "Page 1 of 3"
