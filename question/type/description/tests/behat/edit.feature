@qtype @qtype_description
Feature: Test editing a Description question
  As a teacher
  In order to be able to update my Description question
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
      | questioncategory | qtype       | name            | template |
      | Test questions   | description | description-001 | info     |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

  Scenario: Edit a Description question
    When I click on "Edit" "link" in the "description-001" "table_row"
    And I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    Then I should see "You must supply a value here."
    When I set the following fields to these values:
      | Question name    | Edited description-001 name |
    And I press "id_submitbutton"
    Then I should see "Edited description-001 name"
