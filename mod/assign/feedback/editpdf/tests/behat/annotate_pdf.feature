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
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Plugins > Activity modules > Assignment > Feedback plugins > Annotate PDF" in site administration
    And I upload "pix/help.png" file to "" filemanager
    And I upload "pix/docs.png" file to "" filemanager
    When I press "Save changes"
    Then I should see "Changes saved"
    And I follow "Test ghostscript path"
    And I should see "The ghostscript path appears to be OK"
    And I am on site homepage
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your PDF file |
      | assignsubmission_file_enabled | 1 |
      | Maximum number of uploaded files | 2 |
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
    And I click on ".linebutton" "css_element"
    And I click on ".commentcolourbutton" "css_element"
    And I click on "//img[@alt=\"Blue\"]/parent::button" "xpath_element"
    And I wait until the page is ready
    And I press "Save changes"
    And I wait until the page is ready
    And I should see "The changes to the grade and feedback were saved"

  @javascript
  Scenario: Submit a PDF file as a student in a team and annotate the PDF as a teacher
    Given ghostscript is installed
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
      | student4 | Student | 4 | student4@example.com |
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
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your PDF file |
      | assignsubmission_file_enabled | 1 |
      | Maximum number of uploaded files | 2 |
      | Students submit in groups | Yes |
      | Grouping for student groups | G1 |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I upload "mod/assign/feedback/editpdf/tests/fixtures/submission.pdf" file to "File submissions" filemanager
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I should see "submission.pdf"
    And I should see "Not graded"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I open the action menu in "Student 2" "table_row"
    And I click on "Grade" "link" in the "Student 2" "table_row"
    And I wait for the complete PDF to load
    And I click on ".linebutton" "css_element"
    And I draw on the pdf
    And I press "Save changes"
    And I should see "The changes to the grade and feedback were saved"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "View annotated PDF..." in the "student2@example.com" "table_row"
