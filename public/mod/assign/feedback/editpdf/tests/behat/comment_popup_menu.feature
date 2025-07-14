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
    And the following "activity" exists:
      | activity                            | assign                |
      | course                              | C1                    |
      | name                                | Test assignment name  |
      | assignsubmission_file_enabled       | 1                     |
      | assignsubmission_file_maxfiles      | 1                     |
      | assignsubmission_file_maxsizebytes  | 102400                |
      | assignfeedback_editpdf_enabled      | 1                     |
      | submissiondrafts                    | 0                     |
    And the following "mod_assign > submission" exists:
      | assign  | Test assignment name                                       |
      | user    | student1                                                   |
      | file    | mod/assign/feedback/editpdf/tests/fixtures/submission.pdf  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I change window size to "large"
    And I go to "Submitted for grading" "Test assignment name" activity advanced grading page
    And I change window size to "medium"
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
