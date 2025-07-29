@qtype @qtype_essay
Feature: In a essay question, limit submittable file types
  In order to constrain student submissions for marking
  As a teacher
  I need to limit the submittable file types

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
      | student  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
      | student | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    | defaultmark |
      | Test questions   | essay       | TF1   | First question  | 20          |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | grade |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 20    |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And I am on the "Quiz 1" "mod_quiz > edit" page logged in as teacher
    And I click on "Edit question TF1" "link"
    And I set the field "Allow attachments" to "1"
    And I set the field "Response format" to "No online text"
    And I set the field "Require attachments" to "1"
    And I set the field "filetypeslist[filetypes]" to ".txt"
    And I press "Save changes"
    And I am on the "Quiz 1" "mod_quiz > edit" page
    And I set the field "version" to "Always latest"
    Then I log out

  @javascript
  Scenario: Preview an Essay question and submit a response with a correct filetype.
    Given the following "user private file" exists:
      | user     | student                      |
      | filepath | lib/tests/fixtures/empty.txt |
    When I am on the "Quiz 1" "quiz activity" page logged in as "student"
    And I press "Attempt quiz"
    And I should see "First question"
    And I should see "You can drag and drop files here to add them."
    And I click on "Add..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "empty.txt" "link"
    And I click on "Select this file" "button"
    # Wait for the page to "settle".
    And I wait until the page is ready
    Then I should not see "These file types are not allowed here:"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    Then I should see "empty.txt"
    And following "empty.txt" should download a file that:
      | contains text | empty file for testing purposes |

  @javascript
  Scenario: Preview an Essay question and try to submit a response with an incorrect filetype.
    Given the following "user private file" exists:
      | user     | student                             |
      | filepath | lib/tests/fixtures/upload_users.csv |
    When I am on the "Quiz 1" "quiz activity" page logged in as "student"
    And I press "Attempt quiz"
    And I should see "First question"
    And I should see "You can drag and drop files here to add them."
    And I click on "Add..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    Then I should see "No files available"
