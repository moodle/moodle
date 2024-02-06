@mod @mod_lesson @javascript @editor_tiny
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
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And the following "user private files" exist:
      | user     | filepath                                  | filename        |
      | teacher1 | mod/lesson/tests/fixtures/moodle_logo.jpg | moodle_logo.jpg |
    And I log in as "teacher1"

  Scenario: Duplicate content page with an image.
    When I am on the "Test lesson name" "lesson activity" page
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | Previous page |
      | id_jumpto_1 | Previous page |
    And I click on "Image" "button" in the "Page contents" "form_row"
    And I click on "Browse repositories" "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "How would you describe this image to someone who can't see it:" to "It's the logo"
    And I click on "Save" "button" in the "Image details" "dialogue"
    And I press "Save page"
    And I follow "Duplicate page: First page name"
    Then I should see "Inserted page: First page name"
    And I follow "Update page: First page name"
    And I set the field "Page title" to "Introduction page"
    And I press "Save page"
    And I follow "Update page: First page name"
    Then I should see "First page name"
    And I switch to the "Page contents" TinyMCE editor iframe
    Then "//*[contains(@data-id, 'id_contents_editor')]//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist

  Scenario: Duplicate question page with image in answer.
    When I am on the "Test lesson name" "lesson activity" page
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True false with an image in the answer |
      | Page contents | Select the picture |
      | id_answer_editor_0 | Answer text |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | 1 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    And I click on "Image" "button" in the "//*[@id='id_answer_editor_0']/ancestor::*[@data-fieldtype='editor']" "xpath_element"
    And I click on "Browse repositories" "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "How would you describe this image to someone who can't see it:" to "It's the logo"
    And I click on "Save" "button" in the "Image details" "dialogue"
    And I press "Save page"
    And I follow "Duplicate page: True false with an image in the answer"
    Then I should see "Inserted page: True false with an image in the answer"
    And I follow "Update page: True false with an image in the answer"
    And I set the field "Page title" to "First true false"
    And I press "Save page"
    And I follow "Update page: True false with an image in the answer"
    Then I should see "True false with an image in the answer"
    And I switch to the "Page contents" TinyMCE editor iframe
    Then I should see "Select the picture"
    And I switch to the main frame
    And I switch to the "Answer" TinyMCE editor iframe
    Then "//*[contains(@data-id, 'id_answer_editor_0')]//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist

  Scenario: Duplicate question page with image in feedback.
    When I am on the "Test lesson name" "lesson activity" page
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True false with an image in the feedback |
      | Page contents | Select the picture |
      | id_answer_editor_0 | Answer text |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | 1 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    And I click on "Image" "button" in the "//*[@id='id_response_editor_0']/ancestor::*[@data-fieldtype='editor']" "xpath_element"
    And I click on "Browse repositories" "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle_logo.jpg" "link"
    And I click on "Select this file" "button"
    And I set the field "How would you describe this image to someone who can't see it:" to "It's the logo"
    And I click on "Save" "button" in the "Image details" "dialogue"
    And I press "Save page"
    And I follow "Duplicate page: True false with an image in the feedback"
    Then I should see "Inserted page: True false with an image in the feedback"
    And I follow "Update page: True false with an image in the feedback"
    And I set the field "Page title" to "First true false"
    And I press "Save page"
    And I follow "Update page: True false with an image in the feedback"
    Then I should see "True false with an image in the feedback"
    And I switch to the "Page contents" TinyMCE editor iframe
    Then I should see "Select the picture"
    And I switch to the main frame
    And I switch to the "Response" TinyMCE editor iframe
    Then "//*[contains(@data-id, 'id_response_editor_0')]//img[contains(@src, 'moodle_logo.jpg')]" "xpath_element" should exist
