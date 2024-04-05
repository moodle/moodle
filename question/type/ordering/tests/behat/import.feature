@qtype @qtype_ordering
Feature: Test importing Ordering questions
  As a teacher
  In order to reuse Ordering questions
  I need to import them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript @_file_upload
  Scenario: import Matching question.
    When I am on the "Course 1" "core_question > course question import" page logged in as teacher1
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/ordering/tests/fixtures/testquestion.moodle.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    And I press "Continue"
    Then I should see "dd-ordering 1"
    And I choose "Edit question" action for "dd-ordering 1" in the question bank
    Then the following fields match these values:
      | shownumcorrect          | 1 |
      | id_hintshownumcorrect_0 | 1 |
      | id_hintoptions_0        | 0 |
      | id_hintshownumcorrect_1 | 0 |
      | id_hintoptions_1        | 1 |

  @javascript @_file_upload
  Scenario: Import old question.
    When I am on the "Course 1" "core_question > course question import" page logged in as teacher1
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/ordering/tests/fixtures/testoldquestion.moodle.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    And I press "Continue"
    Then I should see "dd-ordering old question"
    And I choose "Edit question" action for "dd-ordering old question" in the question bank
    Then the following fields match these values:
      | shownumcorrect          | 1 |
      | id_hintshownumcorrect_0 | 1 |
      | id_hintoptions_0        | 1 |
      | id_hintshownumcorrect_1 | 1 |
      | id_hintoptions_1        | 1 |
