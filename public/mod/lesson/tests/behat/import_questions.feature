@mod @mod_lesson
Feature: Import questions into a lesson from multiple formats
  As a teacher
  I want to import question files in different formats into a lesson so images and audio are preserved

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name             | course |
      | lesson   | Test lesson name | C1     |

  @javascript @_file_upload
  Scenario: Imports questions using GIFT format and edits page contents
    Given I am on the "Test lesson name" "lesson activity" page logged in as "teacher1"
    And I follow "Import questions"
    And I set the field "File format" to "GIFT"
    When I upload "mod/lesson/tests/fixtures/moodle_questions.gift" file to "Upload" filemanager
    And I press "Import"
    And I press "Continue"
    Then I should see "Lesson is currently being previewed."
    And I should see "Match the activity to the description."
    And I should see "An activity supporting asynchronous discussions."
    And I should see "A teacher asks a question and specifies a choice of multiple responses."
    And I press "Edit page contents"
    And I set the field "Page contents" to "Match the activity to the description (Edited)"
    And I press "Save page"
    And I should see "Match the activity to the description (Edited)"

  @javascript @_file_upload
  Scenario: Import questions in XML format with images and audio in answers and responses
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Import questions"
    And I set the field "File format" to "Moodle XML format"
    When I upload "mod/lesson/tests/fixtures/multichoice.xml" file to "Upload" filemanager
    And I press "Import"
    Then I should see "Importing 1 questions"
    And I should see " Listen to this greeting:"
    And I should see "What language is being spoken?"
    And I press "Continue"
    And I should see "What language is being spoken?"
    And "//audio[contains(@title, 'Listen to this greeting:')]/source[contains(@src, 'bonjour.mp3')]" "xpath_element" should exist
    And "//*[contains(@class, 'answeroption')]//img[contains(@src, 'pluginfile.php')]" "xpath_element" should exist
    And "//*[contains(@class, 'answeroption')]//img[contains(@src, 'flag-france.jpg')]" "xpath_element" should exist

  @javascript @_file_upload
  Scenario: Import fill in the blank question in a lesson
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Import questions"
    And I set the field "File format" to "Blackboard"
    When I upload "mod/lesson/tests/fixtures/sample_blackboard_fib_qti.dat" file to "Upload" filemanager
    And I press "Import"
    Then I should see "Importing 1 questions"
    And I should see "Name an amphibian: __________"
    And I press "Continue"
    And I should not see "__________"
    And I should not see "Your answer"
    And I set the field "id_answer" to "frog"
    And I press "Submit"
    And I should see "Your answer : frog"
    And I should see "A frog is an amphibian"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
