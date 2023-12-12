@qbank @qbank_previewquestion
Feature: A teacher can preview questions in the question bank
  In order to ensure the questions are properly created
  As a teacher
  I need to preview the questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name                          |
      | Test questions   | numerical | Test question to be previewed |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    When I choose "Preview" action for "Test question to be previewed" in the question bank

  Scenario: Question preview shows the question and other information
    Then the state of "What is pi to two d.p.?" question is shown as "Not yet answered"
    And I should see "(latest)"
    And I should see "Marked out of 1.00"
    And I should see "Technical information"
    And I should see "Display options"
    And I should see "Preview options"
    And I should see "Comments"
    And I click on "Comments" "link"
    And I should see "Save comment"
    And I should see "ID number"
    And "Numerical" "icon" should exist
    And I should see "Version"
    And I click on "Preview options" "link"
    And I should see "These settings are for testing the question. The options you select only affect the preview."
    And I should see "Question version"

  Scenario: Preview lets the teacher see what happens when an answer is saved
    When I set the field "Answer:" to "1"
    And I press "Save"
    Then the state of "What is pi to two d.p.?" question is shown as "Answer saved"

  Scenario: Preview lets the teacher see what happens when an answer is submitted
    When I set the field "Answer:" to "3.14"
    And I press "Submit and finish"
    Then the state of "What is pi to two d.p.?" question is shown as "Correct"

  Scenario: Preview lets the teacher see what happens with different review options
    Given I set the field "Answer:" to "3.14"
    And I press "Submit and finish"
    And I press "Display options"
    When I set the field "Whether correct" to "Not shown"
    And I set the field "Decimal places in grades" to "5"
    And I press "Update display options"
    And I set the field "Answer:" to "3.14"
    And I press "Submit and finish"
    Then the state of "What is pi to two d.p.?" question is shown as "Complete"
    And I should see "1.00000"

  Scenario: Preview lets the teacher see what happens with different behaviours
    When I press "Preview options"
    And I set the field "How questions behave" to "Immediate feedback"
    And I set the field "Marked out of" to "3"
    And I press "Save preview options and start again"
    And I set the field "Answer:" to "3.1"
    And I press "Check"
    Then the state of "What is pi to two d.p.?" question is shown as "Incorrect"
    And I should see "Mark 0.00 out of 3.00"
    And I should see "Not accurate enough."

  Scenario: Preview lets the teacher "Start again" while previewing
    Given I set the field "Answer:" to "1"
    And I press "Submit and finish"
    When I press "Start again"
    Then the state of "What is pi to two d.p.?" question is shown as "Not yet answered"

  Scenario: Preview lets the teacher "Fill in correct response" while previewing
    When I press "Fill in correct responses"
    Then the field "Answer:" matches value "3.14"

  Scenario: Preview a question with very small grade
    When I press "Preview options"
    And I set the field "Marked out of" to "0.00000123456789"
    And I press "Save preview options and start again"
    Then the field "Marked out of" matches value "0.00000123456789"

  Scenario: Question version is updated when edited and teacher can change question version
    Given I should see "Version 1"
    And I press "Close preview"
    And I choose "Edit question" action for "Test question to be previewed" in the question bank
    And I set the field "Question name" to "New version"
    And I set the field "Question text" to "New text version"
    And I click on "submitbutton" "button"
    And I choose "Preview" action for "New version" in the question bank
    When I expand all fieldsets
    And I should see "Version 2"
    And I should see "(latest)"
    And I should see "New version"
    And I should see "New text version"
    And I should not see "Test question to be previewed"
    And I should not see "Version 1"
    And I should see "1" in the "Question version" "select"
    And I should see "2" in the "Question version" "select"
    And I set the field "Question version" to "1"
    And I press "Save preview options and start again"
    Then I should see "Version 1"
    And I should not see "Version 2"
    And I should not see "(latest)"

  Scenario: The preview always uses the latest question version by default.
    Given the following "core_question > updated questions" exist:
      | questioncategory | question                      | questiontext       |
      | Test questions   | Test question to be previewed | Question version 2 |
    And I should see "Version 1 (latest)"
    And I expand all fieldsets
    And the field "Question version" matches value "Always latest"
    And I set the field "Answer:" to "3.14"
    And I press "Submit and finish"
    And I should see "Version 1"
    When I press "Start again"
    Then I should not see "Version 1"
    And I should see "Version 2 (latest)"

  Scenario: Detect a newer version during always latest preview and offer to switch to the latest
    Given I should not see "This preview is using version 1"
    And the following "core_question > updated questions" exist:
      | questioncategory | question                      | questiontext       |
      | Test questions   | Test question to be previewed | Question version 2 |
    And I should see "Version 1 (latest)"
    And I set the field "Answer:" to "3.14"
    When I press "Submit and finish"
    And I should see "This preview is using an older version of the question."
    And I press "Use latest version"
    Then I should not see "Version 1"
    And I should see "Version 2 (latest)"

  Scenario: Previewing from the question history will not always show the latest version
    Given I press "Close preview"
    And the following "core_question > updated questions" exist:
      | questioncategory | question                      | questiontext       |
      | Test questions   | Test question to be previewed | Question version 2 |
    And I choose "History" action for "Test question to be previewed" in the question bank
    And I choose "Preview" action for "Test question to be previewed" in the question bank
    And I should see "Version 1 (of 2)"
    And I expand all fieldsets
    And the field "Question version" matches value "1"
    And I set the field "Answer:" to "3.14"
    And I press "Submit and finish"
    And I should see "Version 1 (of 2)"
    And I should not see "The latest version is 2."
    And the following "core_question > updated questions" exist:
      | questioncategory | question                      | questiontext       |
      | Test questions   | Test question to be previewed | Question version 3 |
    When I press "Start again"
    Then I should see "Version 1 (of 3)"
    And I should not see "Version 3 (latest)"

  Scenario: Question preview can be closed
    And I press "Close preview"
    Then I should not see "(latest)"
    And I should see "Test quiz"
