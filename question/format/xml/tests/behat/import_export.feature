@qformat @qformat_xml
Feature: Test importing questions from Moodle XML format.
  In order to reuse questions
  As an teacher
  I need to be able to import them in XML format.

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
  Scenario: import some true/false questions from Moodle XML format
    When I navigate to "Question bank" in current page administration
    And I select "Import" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_xml" to "1"
    And I upload "question/format/xml/tests/fixtures/truefalse.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 2 questions from file"
    And I should see "is an acronym for Modular Object-Oriented Dynamic Learning Education"
    And I should see "is an acronym for Modular Object-Oriented Dynamic Learning Environment"
    When I press "Continue"
    Then I should see "Moodle acronym (False)"
    Then I should see "Moodle acronym (True)"

    # Now export again.
    And I am on "Course 1" course homepage
    When I navigate to "Question bank" in current page administration
    And I select "Export" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_xml" to "1"
    And I set the field "Export category" to "TrueFalse"
    And I press "Export questions to file"
    And following "click here" should download between "57100" and "58150" bytes

  @javascript @_file_upload
  Scenario: import some multiple choice questions from Moodle XML format
    When I navigate to "Question bank" in current page administration
    And I select "Import" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_xml" to "1"
    And I upload "question/format/xml/tests/fixtures/multichoice.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "What language is being spoken?"
    When I press "Continue"
    Then I should see "Greeting"

  @javascript @_file_upload
  Scenario: import some multi-answer questions from Moodle XML format
    When I navigate to "Question bank" in current page administration
    And I select "Import" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_xml" to "1"
    And I upload "question/format/xml/tests/fixtures/multianswer.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "Match the following cities with the correct state,"
    When I press "Continue"
    Then I should see "cloze with images"

  @javascript @_file_upload
  Scenario: import some questions with legacy-style images from Moodle XML format
    When I navigate to "Question bank" in current page administration
    And I select "Import" from the "Question bank tertiary navigation" singleselect
    And I set the field "id_format_xml" to "1"
    And I upload "question/format/xml/tests/fixtures/sample_questions_with_old_image_tag.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 2 questions from file"
    And I should see "This is a multianswer question with an image in the old"
    And I should see "This is a multichoice question with an image in the old"
    When I press "Continue"
    Then I should see "cloze question with image"
    Then I should see "mcq with image"
