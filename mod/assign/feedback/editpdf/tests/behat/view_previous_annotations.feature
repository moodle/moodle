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
    And the following "activity" exists:
      | activity                           | assign               |
      | course                             | C1                   |
      | name                               | Test assignment name |
      | maxattempts                        | 0                    |
      | assignsubmission_file_enabled      | 1                    |
      | assignsubmission_file_maxfiles     | 2                    |
      | assignsubmission_file_maxsizebytes | 102400               |
      | assignfeedback_editpdf_enabled     | 1                    |
      | submissiondrafts                   | 0                    |
      | maxattempts                        | -1                   |
      | attemptreopenmethod                | manual               |
    And the following "mod_assign > submission" exists:
      | assign  | Test assignment name                                                                                              |
      | user    | student1                                                                                                          |
      | file    | mod/assign/feedback/editpdf/tests/fixtures/submission.pdf, mod/assign/feedback/editpdf/tests/fixtures/testgs.pdf  |

    When I am on the "Test assignment name" Activity page logged in as teacher1
    And I change window size to "large"
    And I go to "Submitted for grading" "Test assignment name" activity advanced grading page
    And I change window size to "medium"
    Then I should see "Page 1 of 3"
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
