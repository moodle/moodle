@qtype @qtype_essay
Feature: Test editing an Essay question
  As a teacher
  In order to be able to update my Essay question
  I need to edit them

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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name      | template         |
      | Test questions   | essay | essay-001 | editor           |
      | Test questions   | essay | essay-002 | editorfilepicker |
      | Test questions   | essay | essay-003 | plain            |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" node in "Course administration"

  Scenario: Edit an Essay question
    When I click on "Edit" "link" in the "essay-001" "table_row"
    And I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    Then I should see "You must supply a value here."
    When I set the following fields to these values:
      | Question name   | Edited essay-001 name |
      | Response format | No online text        |
    And I press "id_submitbutton"
    Then I should see "When \"No online text\" is selected, or responses are optional, you must allow at least one attachment."
    When I set the following fields to these values:
      | Response format | Plain text |
    And I press "id_submitbutton"
    Then I should see "Edited essay-001 name"
