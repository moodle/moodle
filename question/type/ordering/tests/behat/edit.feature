@qtype @qtype_ordering
Feature: Test editing an Ordering question
  As a teacher
  In order to be able to update my Ordering question
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
      | questioncategory | qtype    | name                 | template |
      | Test questions   | ordering | Ordering for editing | moodle   |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript @_switch_window
  Scenario: Edit an Ordering question
    When I choose "Edit question" action for "Ordering for editing" in the question bank
    And I set the following fields to these values:
      | Question name ||
    And I press "id_submitbutton"
    Then I should see "You must supply a value here."
    When I set the following fields to these values:
      | Question name | Edited Ordering |
    And I press "id_submitbutton"
    Then I should see "Edited Ordering"

  @javascript @_switch_window
  Scenario: Editing an ordering question and making sure the form does not allow duplication of draggables
    When I choose "Edit question" action for "Ordering for editing" in the question bank
    And I set the following fields to these values:
      | Draggable item 4 | Object |
    And I press "id_submitbutton"
    Then  I should see "Duplication of draggable items is not allowed. The string \"Object\" is already used in Draggable item 2."
    Given I set the following fields to these values:
      | Draggable item 4 | Dynamic |
    And I press "id_submitbutton"
    Then I should see "Ordering for editing"
