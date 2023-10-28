@qbank @qbank_previewquestion
Feature: A teacher can export a question as XML from the preview question screen
  To help reuse questions
  As a teacher
  I can easily export the question I am previewing

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | name           | contextlevel | reference |
      | Test questions | Course       | C1        |
    And the following "questions" exist:
      | questioncategory | qtype     | name                          |
      | Test questions   | numerical | Test question to be previewed |

  Scenario: Question preview shows the question and other information
    When I am on the "Test question to be previewed" "core_question > preview" page logged in as teacher
    Then the state of "What is pi to two d.p.?" question is shown as "Not yet answered"
    And "Download this question in Moodle XML format" "link" should exist
