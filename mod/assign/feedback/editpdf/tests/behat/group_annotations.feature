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
    And the following "activity" exists:
      | activity                            | assign                |
      | course                              | C1                    |
      | name                                | Test assignment name  |
      | assignsubmission_file_enabled       | 1                     |
      | assignsubmission_file_maxfiles      | 1                     |
      | assignsubmission_file_maxsizebytes  | 102400                |
      | assignfeedback_editpdf_enabled      | 1                     |
      | submissiondrafts                    | 0                     |
      | teamsubmission                      | 1                     |
    And the following "mod_assign > submission" exists:
      | assign  | Test assignment name                                       |
      | user    | student1                                                   |
      | file    | mod/assign/feedback/editpdf/tests/fixtures/submission.pdf  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I go to "Submitted for grading" "Test assignment name" activity advanced grading page
    And I wait for the complete PDF to load
    And I click on ".navigate-next-button" "css_element"
    And I wait until the page is ready
    And I click on ".stampbutton" "css_element"
    And I draw on the pdf
    And I wait until the page is ready

  @javascript
  Scenario: Submit a PDF file as a student and annotate the PDF as a teacher
    Given I set the field "applytoall" to "0"
    And I press "Save changes"
    And I should see "The changes to the grade and feedback were saved"
    And I click on "Edit settings" "link"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    When I follow "View annotated PDF..."
    Then I should see "Annotate PDF"
    And I wait until the page is ready
    And I click on "Close" "button" in the "Annotate PDF" "dialogue"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student2
    And I should not see "View annotated PDF..."

  @javascript
  Scenario: Submit a PDF file as a student and annotate the PDF as a teacher and all students in the group get a copy of the annotated PDF.
    Given I press "Save changes"
    And I should see "The changes to the grade and feedback were saved"
    And I am on the "Test assignment name" Activity page
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    When I follow "View annotated PDF..."
    And I change window size to "large"
    Then I should see "Annotate PDF"
    And I change window size to "medium"
    And I wait until the page is ready
    And I click on "Close" "button" in the "Annotate PDF" "dialogue"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student2
    And I should see "View annotated PDF..."
