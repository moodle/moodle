@mod @mod_assign @assignfeedback @assignfeedback_editpdf @_file_upload
Feature: In an assignment, teacher can annotate PDF files during grading
  In order to provide visual report on a graded PDF
  As a teacher
  I need to use the PDF editor

  @javascript
  Scenario: Submit a PDF file as a student and annotate the PDF as a teacher
    Given ghostscript is installed
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I expand "Site administration" node
    And I expand "Plugins" node
    And I expand "Activity modules" node
    And I expand "Assignment" node
    And I expand "Feedback plugins" node
    And I follow "Annotate PDF"
    And I upload "pix/help.png" file to "" filemanager
    And I upload "pix/docs.png" file to "" filemanager
    When I press "Save changes"
    Then I should see "Changes saved"
    And I follow "Test ghostscript path"
    And I should see "The ghostscript path appears to be OK"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your PDF file |
      | assignsubmission_file_enabled | 1 |
      | Maximum number of uploaded files | 2 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
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
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I click on "Grade" "link" in the "Submitted for grading" "table_row"
    And I follow "Launch PDF editor..."
    And I click on ".navigate-next-button" "css_element"
    And I click on ".stampbutton" "css_element"
    And I click on ".linebutton" "css_element"
    And I click on ".commentcolourbutton" "css_element"
    And I click on "//img[@alt=\"Blue\"]" "xpath_element"
    And I click on "Close" "button"
    And I press "Save changes"
    And I should see "The grade changes were saved"

  @javascript
  Scenario: Submit a PDF file as a student in a team and annotate the PDF as a teacher
    Given ghostscript is installed
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | student3 | Student | 3 | student3@asd.com |
      | student4 | Student | 4 | student4@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | G1 | C1 | G1 |
      | G2 | C1 | G2 |
    And the following "groupings" exist:
      | name | course | idnumber |
      | G1   | C1     | G1       |
    And the following "group members" exist:
      | user        | group |
      | student1    | G1  |
      | student2    | G1  |
      | student3    | G2  |
      | student4    | G2  |
    And the following "grouping groups" exist:
      | grouping | group |
      | G1       | G1    |
      | G1       | G2    |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your PDF file |
      | assignsubmission_file_enabled | 1 |
      | Maximum number of uploaded files | 2 |
      | Students submit in groups | Yes |
      | Grouping for student groups | G1 |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I upload "mod/assign/feedback/editpdf/tests/fixtures/submission.pdf" file to "File submissions" filemanager
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I should see "submission.pdf"
    And I should see "Not graded"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I click on "Grade" "link" in the "Student 2" "table_row"
    And I follow "Launch PDF editor..."
    And I click on ".stampbutton" "css_element"
    And I click on ".drawingcanvas" "css_element"
    And I click on "Close" "button"
    And I press "Save changes"
    And I should see "The grade changes were saved"
    And I press "Continue"
    And I should see "View annotated PDF..." in the "Student 1" "table_row"
