@mod @mod_lesson
Feature: In a lesson activity, a teacher can duplicate a lesson page
  In order to duplicate a lesson page
  As a teacher
  I need to add content to a lesson

  Background:
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
    And I log in as "teacher1"
    And I follow "Manage private files"
    And I upload "mod/lesson/tests/fixtures/moodle_logo.jpg" file to "Files" filemanager
    And I click on "Save changes" "button"

  @javascript @_file_upload
  Scenario: Duplicate content page with an image.
    Given I am on the "Test lesson name" "lesson activity" page
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | Previous page |
      | id_jumpto_1 | Previous page |
    # Atto needs focus to add image, select empty p tag to do so.
    And I select the text in the "id_contents_editor" Atto editor
    And I click on "Insert or edit image" "button" in the "[data-fieldtype=editor]" "css_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "It's the logo"
    And I click on "Save image" "button"
    And I press "Save page"
    And I follow "Duplicate page: First page name"
    And I should see "Inserted page: First page name"
    And I follow "Update page: First page name"
    And I set the field "Page title" to "Introduction page"
    And I press "Save page"
    When I follow "Update page: First page name"
    And I should see "First page name"
    Then "//*[contains(@id, 'id_contents_editor')]//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist

  @javascript @_file_upload
  Scenario: Duplicate question page with image in answer.
    Given I am on the "Test lesson name" "lesson activity" page
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True false with an image in the answer |
      | Page contents | Select the picture |
      | id_answer_editor_0 | To be replaced |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | 1 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    # Atto needs focus to add image, select empty p tag to do so.
    And I select the text in the "id_answer_editor_0" Atto editor
    And I click on "Insert or edit image" "button" in the "//*[@id='id_answer_editor_0']/ancestor::*[@data-fieldtype='editor']" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "It's the logo"
    And I click on "Save image" "button"
    And I press "Save page"
    And I follow "Duplicate page: True false with an image in the answer"
    And I should see "Inserted page: True false with an image in the answer"
    And I follow "Update page: True false with an image in the answer"
    And I set the field "Page title" to "First true false"
    And I press "Save page"
    When I follow "Update page: True false with an image in the answer"
    And I should see "True false with an image in the answer"
    And I should see "Select the picture"
    Then "//*[contains(@id, 'id_answer_editor_0')]//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist

  @javascript @_file_upload
  Scenario: Duplicate question page with image in feedback.
    Given I am on the "Test lesson name" "lesson activity" page
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True false with an image in the feedback |
      | Page contents | Select the picture |
      | id_answer_editor_0 | To be replaced |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | 1 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    # Atto needs focus to add image, select empty p tag to do so.
    And I select the text in the "id_response_editor_0" Atto editor
    And I click on "Insert or edit image" "button" in the "//*[@id='id_response_editor_0']/ancestor::*[@data-fieldtype='editor']" "xpath_element"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "It's the logo"
    And I click on "Save image" "button"
    And I press "Save page"
    And I follow "Duplicate page: True false with an image in the feedback"
    And I should see "Inserted page: True false with an image in the feedback"
    And I follow "Update page: True false with an image in the feedback"
    And I set the field "Page title" to "First true false"
    And I press "Save page"
    When I follow "Update page: True false with an image in the feedback"
    And I should see "True false with an image in the feedback"
    And I should see "Select the picture"
    Then "//*[contains(@id, 'id_response_editor_0')]//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist
