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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    | defaultmark |
      | Test questions   | essay       | TF1   | First question  | 20          |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | grade |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 20    |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And I am on the "Quiz 1" "mod_quiz > View" page logged in as "student1"
    And I press "Attempt quiz now"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
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

  @javascript @_switch_window @_file_upload @_bug_phantomjs
  Scenario: Comment on a response to an essay question attempt.
    When I log in as "teacher1"
    And I follow "Manage private files"
    And I upload "mod/quiz/tests/fixtures/moodle_logo.jpg" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on the "Quiz 1 > student1 > Attempt 1" "mod_quiz > Attempt review" page
    And I follow "Make comment or override mark"
    And I switch to "commentquestion" window
    And I set the field "Comment" to "Administrator's comment"
    # Atto needs focus to add image, select empty p tag to do so.
    And I select the text in the "Comment" Atto editor
    And I click on "Insert or edit image" "button" in the "[data-fieldtype=editor]" "css_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "It's the logo"
    And I click on "Save image" "button"
    # Editor is not inserting the html for the image correctly
    # when running under behat so line below manually inserts it.
    And I set the field "Comment" to "<img src=\"@@PLUGINFILE@@/moodle_logo.jpg\" alt=\"It's the logo\" width=\"48\" height=\"48\" class=\"img-fluid atto_image_button_text-bottom\"><!-- File hash: a8e3ffba4ab315b3fb9187ebbf122fe9 -->"
    And I press "Save" and switch to main window
    And I switch to the main window
    And I should see "Commented: [It's the logo]" in the ".history table" "css_element"
    And "img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist in the ".comment" "css_element"
    # This time is same as time the window is open. So wait for it to close before proceeding.
    And I wait "2" seconds
