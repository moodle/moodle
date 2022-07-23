@qformat @qformat_gift
Feature: Test importing questions from GIFT format.
  In order to reuse questions
  As an teacher
  I need to be able to import them in GIFT format.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And I log in as "teacher"
    And I am on "Course 1" course homepage

  @javascript @_file_upload
  Scenario: import some GIFT questions
    When I navigate to "Question bank" in current page administration
    And I select "Import" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_gift" to "1"
    And I upload "question/format/gift/tests/fixtures/questions.gift.txt" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 9 questions from file"
    And I should see "What's between orange and green in the spectrum?"
    When I press "Continue"
    Then I should see "colours"

    # Now export again.
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration
    And I select "Export" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_gift" to "1"
    And I press "Export questions to file"
    And following "click here" should download between "1500" and "1800" bytes

  @javascript @_file_upload
  Scenario: import a GIFT file which specifies the category
    When I navigate to "Question bank" in current page administration
    And I select "Import" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_gift" to "1"
    And I upload "question/format/gift/tests/fixtures/questions_in_category.gift.txt" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 4 questions from file"
    And I should see "Match the activity to the description."
    When I press "Continue"
    Then I should see "Moodle activities"

  @javascript @_file_upload
  Scenario: import some GIFT questions with unsupported encoding
    When I navigate to "Question bank" in current page administration
    And I select "Import" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_gift" to "1"
    And I upload "question/format/gift/tests/fixtures/questions_encoding_windows-1252.gift.txt" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "The file you selected does not use UTF-8 character encoding. GIFT format files must use UTF-8."
