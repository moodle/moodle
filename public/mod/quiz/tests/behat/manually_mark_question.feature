@mod @mod_quiz
Feature: Teachers can override the grade for any question
  As a teacher
  In order to correct errors
  I must be able to override the grades that Moodle gives.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student0@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber | grade |
      | quiz       | Quiz 1  | Quiz 1 description | C1     | quiz1    | 20    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    | defaultmark |
      | Test questions   | essay       | TF1   | First question  | 20          |

    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And the following "user private files" exist:
      | user     | filepath                                |
      | teacher1 | mod/quiz/tests/fixtures/moodle_logo.jpg |
    And I am on the "Quiz 1" "mod_quiz > View" page logged in as "student1"
    And I press "Attempt quiz"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I log out

  @javascript @_switch_window @_bug_phantomjs
  Scenario: Validating the marking of an essay question attempt.
    When I am on the "Quiz 1 > student1 > Attempt 1" "mod_quiz > Attempt review" page logged in as "teacher1"
    And I follow "Make comment or override mark"
    And I switch to "commentquestion" window
    And I set the field "Mark" to "25"
    And I press "Save"
    Then I should see "This grade is outside the valid range."
    And I set the field "Mark" to "aa"
    And I press "Save"
    And I should see "That is not a valid number."
    And I set the field "Mark" to "10.0"
    And I press "Save" and switch to main window
    And I should see "Complete" in the "Manually graded 10 with comment: " "table_row"
    And I follow "Make comment or override mark"
    And I switch to "commentquestion" window
    And I should see "Teacher 1" in the "Manually graded 10 with comment: " "table_row"

  @javascript @_switch_window @_file_upload @_bug_phantomjs @editor_tiny
  Scenario: Comment on a response to an essay question attempt.
    When I log in as "teacher1"
    And I am on the "Quiz 1 > student1 > Attempt 1" "mod_quiz > Attempt review" page
    And I follow "Make comment or override mark"
    And I switch to "commentquestion" window
    And "Summary of attempt" "table" should exist
    And I set the field "Comment" to "Administrator's comment"
    And I select the "p" element in position "0" of the "Comment" TinyMCE editor
    And I click on the "Image" button for the "Comment" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "How would you describe this image to someone who can't see it?" to "It's the logo"
    And I click on "Save" "button" in the "Image details" "dialogue"
    And I press "Save" and switch to main window
    And I switch to the main window
    Then I should see "Commented: [It's the logo]" in the ".history table" "css_element"
    And "//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist in the ".comment" "css_element"
    # This time is same as time the window is open. So wait for it to close before proceeding.
    And I wait "2" seconds
