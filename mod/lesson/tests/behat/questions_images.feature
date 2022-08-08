@mod @mod_lesson
Feature: In a lesson activity, teacher can add embedded images in questions answers and responses
  As a teacher
  I need to add questions with images in answers and responses

  # This scenario has Atto-specific steps. See MDL-75913 for further details.
  @javascript @_file_upload @editor_atto
  Scenario: questions with images in answers and responses
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | course   | C1               |
      | activity | lesson           |
      | name     | Test lesson name |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And I log in as "teacher1"
    And I follow "Manage private files"
    And I upload "mod/lesson/tests/fixtures/moodle_logo.jpg" file to "Files" filemanager
    And I click on "Save changes" "button"
    When I am on the "Test lesson name" "lesson activity" page
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Multichoice"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Multichoice question |
      | Page contents | What animal is an amphibian? |
      | id_answer_editor_0 | Frog |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | Next page |
      | id_score_0 | 1 |
      | id_answer_editor_1 | Cat |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
      | id_answer_editor_2 | <p></p><p>Dog</p> |
      | id_response_editor_2 | Incorrect answer |
      | id_jumpto_2 | This page |
      | id_score_2 | 0 |
    # Atto needs focus to add image, select empty p tag to do so.
    And I select the text in the "id_answer_editor_2" Atto editor
    And I click on "Insert or edit image" "button" in the "//*[@data-fieldtype='editor']/*[descendant::*[@id='id_answer_editor_2']]" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "It's the logo"
    And I click on "Save image" "button"
    And I press "Save page"
    And I set the field "qtype" to "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Next question |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | <p></p><p>Correct</p> |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    # Atto needs focus to add image, select empty p tag to do so.
    And I select the text in the "id_response_editor_0" Atto editor
    And I click on "Insert or edit image" "button" in the "//*[@data-fieldtype='editor']/*[descendant::*[@id='id_response_editor_0']]" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "It's the logo"
    And I click on "Save image" "button"
    And I press "Save page"
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "What animal is an amphibian?"
    And "//*[contains(@class, 'answeroption')]//img[contains(@src, 'pluginfile.php')]" "xpath_element" should exist
    And "//*[contains(@class, 'answeroption')]//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist
    And I set the following fields to these values:
      | Cat | 1 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "Correct"
    And I should not see "Wrong"
    And "//img[contains(@src, 'pluginfile.php')]" "xpath_element" should exist in the ".correctanswer" "css_element"
    And "//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist in the ".correctanswer" "css_element"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 2)."
