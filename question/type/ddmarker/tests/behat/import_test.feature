@ou @ou_vle @qtype @qtype_ddmarker
Feature: Test the import functionality of this question type.
  As a manager/teacher I should be able to import questions from other courses to this course

  Background:
     Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com  |
      | manager1 | M1        | Manager1 | manager11@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | manager1 | C1     | manager        |
    And I log in as "teacher1"
    And I follow "Course 1"

  @javascript
  Scenario: import a ddmarker (Drag and drop markers) question.
    When I navigate to "Import" node in "Course administration > Question bank"
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/ddmarker/tests/fixtures/testquestion.moodle.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "Please place the markers on the map of Milton Keynes and be aware that there is more than one railway station."
    And I press "Continue"
    And I should see "Imported Drag and drop markers 001"
