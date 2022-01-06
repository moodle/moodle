@mod @mod_assign @assignfeedback @assignfeedback_editpdf @javascript @_file_upload
Feature: Ensure that a comment remains visible if its popup menu is open
  In order to insert quick list comments in the PDF editor
  As a teacher
  I need the comment to stay visible when its popup menu is open

  Background:
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
      | assignfeedback_editpdf_enabled | 1 |
      | Maximum number of uploaded files  | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I upload "mod/assign/feedback/editpdf/tests/fixtures/submission.pdf" file to "File submissions" filemanager
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
    And I wait for the complete PDF to load

  Scenario: Add an empty comment and open its menu
    When I click on ".commentbutton" "css_element"
    And I draw on the pdf
    And I click on ".commentdrawable a" "css_element"
    Then ".drawingcanvas .commentdrawable" "css_element" should exist

  Scenario: Add text to a comment and open its menu
    When I click on ".commentbutton" "css_element"
    And I draw on the pdf
    And I set the field with xpath "//div[@class='commentdrawable']//textarea" to "Comment"
    And I click on ".commentdrawable a" "css_element"
    Then ".drawingcanvas .commentdrawable" "css_element" should exist
    And the "class" attribute of ".drawingcanvas .commentdrawable" "css_element" should not contain "commentcollapsed"
