@qtype @qtype_multianswer
Feature: Exporting Multianswer (Cloze) questions
  As a teacher
  In order to reuse my questions
  I need to be able to export a Cloze question or a category containing a Cloze question.

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext                                       |
      | Test questions   | multianswer | TF1  | {1:SHORTANSWER:=Berlin} is the capital of Germany. |
    And I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"

  Scenario: Edit a previously created multichoice question
    When I choose "Edit question" action for "TF1" in the question bank
    And I set the following fields to these values:
      | Question name | Capital |
      | Question text | {1:SHORTANSWER:=Berlin} is the capital of Germany. |
    And I press "id_submitbutton"
    Then I should see "Capital"
    And I should not see "TF1"

  @javascript
  Scenario: Export the single multichoice question as XML
    When I choose "Export as Moodle XML" action for "TF1" in the question bank
    #Next step does not work as a check, however, when the download cannot be triggered, an error should appear and stop the test.
    #Then following "Download" should download between "1" and "180000" bytes

  @javascript
  Scenario: Export the category containing the multichoice question as XML
    When I select "Export" from the "Question bank tertiary navigation" singleselect
    And I set the field "Moodle XML format" to "1"
    And I click on "Export questions to file" "button"
    Then following "click here" should download a file that:
      | Has mimetype                 | text/xml |
      | Contains text in xml element | TF1      |
