@mod @mod_lesson
Feature: In a lesson activity, teacher can import embedded images in questions answers and responses
  As a teacher
  I need to import a question with images in answers and responses in a lesson

  @javascript @_file_upload
  Scenario: Import questions with images in answers and responses in a lesson
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
    And I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
      | Description | Test lesson description |
    And I follow "Test lesson name"
    And I follow "Import questions"
    And I set the field "File format" to "Moodle XML format"
    And I upload "mod/lesson/tests/fixtures/multichoice.xml" file to "Upload" filemanager
    And I press "Import"
    Then I should see "Importing 1 questions"
    And I should see " Listen to this greeting:"
    And I should see "What language is being spoken?"
    And I press "Continue"
    And I should see "What language is being spoken?"
    And "//audio[contains(@title, 'Listen to this greeting:')]/source[contains(@src, 'bonjour.mp3')]" "xpath_element" should exist
    And "//*[contains(@class, 'answeroption')]//img[contains(@src, 'pluginfile.php')]" "xpath_element" should exist
    And "//*[contains(@class, 'answeroption')]//img[contains(@src, 'flag-france.jpg')]" "xpath_element" should exist
