@mod @mod_lesson @_file_upload
Feature: Teacher can import questions into a lesson activity

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
      | activity | name          | course |
      | lesson   | Import lesson | C1     |

  @javascript
  Scenario: Teacher imports questions using Moodle XML (image is displayed)
    Given I am on the "Import lesson" "lesson activity" page logged in as "teacher1"
    And I follow "Import questions"
    And I set the field "File format" to "Moodle XML"
    When I upload "mod/lesson/tests/fixtures/multichoice.xml" file to "Upload" filemanager
    And I press "Import"
    And I press "Continue"
    Then I should see "Lesson is currently being previewed."
    And "//img[contains(@src, 'pluginfile.php')]" "xpath_element" should exist
    And I press "Edit page contents"
    And I set the field "Page contents" to "Edited page contents"
    And I press "Save page"
    And I should see "Edited page contents"

  @javascript
  Scenario: Teacher imports questions using GIFT (images are not displayed)
    Given I am on the "Import lesson" "lesson activity" page logged in as "teacher1"
    And I follow "Import questions"
    And I set the field "File format" to "GIFT"
    When I upload "mod/lesson/tests/fixtures/lesson_import_questions.gift" file to "Upload" filemanager
    And I press "Import"
    And I press "Continue"
    Then I should see "Lesson is currently being previewed."
    And "//img[contains(@src, 'pluginfile.php')]" "xpath_element" should not exist
    And I press "Edit page contents"
    And I set the field "Page contents" to "Edited page contents"
    And I press "Save page"
    And I should see "Edited page contents"
