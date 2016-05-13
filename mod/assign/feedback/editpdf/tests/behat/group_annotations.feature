@mod @mod_assign @assignfeedback @assignfeedback_editpdf @_file_upload
Feature: In a group assignment, teacher can annotate PDF files for all users
  In order to provide visual report on a graded PDF for all users
  As a teacher
  I need to use the PDF editor for a group assignment

  Background:
    Given ghostscript is installed
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name     | course | idnumber |
      | G1       | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name                   | Test assignment name |
      | Description                       | Submit your PDF file |
      | assignsubmission_file_enabled     | 1 |
      | Maximum number of uploaded files  | 1 |
      | Students submit in groups         | Yes |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I upload "mod/assign/feedback/editpdf/tests/fixtures/submission.pdf" file to "File submissions" filemanager
    And I press "Save changes"
    And I should see "Submitted for grading"
    And I should see "submission.pdf"
    And I should see "Not graded"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Submitted for grading" "table_row"
    And I click on ".navigate-next-button" "css_element"
    And I click on ".stampbutton" "css_element"
    And I draw on the pdf
    And I wait until the page is ready

  @javascript
  Scenario: Submit a PDF file as a student and annotate the PDF as a teacher
    Given I set the field "applytoall" to "0"
    And I press "Save changes"
    And I should see "The changes to the grade and feedback were saved"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I follow "View annotated PDF..."
    Then I should see "Annotate PDF"
    And I wait until the page is ready
    And I click on "Close" "button"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should not see "View annotated PDF..."

  @javascript
  Scenario: Submit a PDF file as a student and annotate the PDF as a teacher and all students in the group get a copy of the annotated PDF.
    Given I press "Save changes"
    And I click on "Ok" "button"
    And I follow "Course 1"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I follow "View annotated PDF..."
    And I change window size to "large"
    Then I should see "Annotate PDF"
    And I change window size to "medium"
    And I wait until the page is ready
    And I click on "Close" "button"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "View annotated PDF..."
