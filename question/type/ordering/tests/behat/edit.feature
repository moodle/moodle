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

  @javascript
  Scenario: Edit an Ordering question
    When I am on the "Ordering for editing" "core_question > edit" page logged in as teacher1
    And I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    Then I should see "You must supply a value here."
    When I set the following fields to these values:
      | Question name         | Edited Ordering |
      | hintoptions[0]        | 1               |
      | hintoptions[1]        | 0               |
      | hintshownumcorrect[0] | 0               |
      | hintshownumcorrect[1] | 1               |
      | shownumcorrect        | 1               |
    And I press "id_submitbutton"
    Then I should see "Edited Ordering"
    And I choose "Edit question" action for "Edited Ordering" in the question bank
    And the following fields match these values:
      | id_shownumcorrect       | 1 |
      | id_hintshownumcorrect_0 | 0 |
      | id_hintoptions_0        | 1 |
      | id_hintshownumcorrect_1 | 1 |
      | id_hintoptions_1        | 0 |

  @javascript
  Scenario: Editing an ordering question and making sure the form does not allow duplication of draggables
    When I am on the "Ordering for editing" "core_question > edit" page logged in as teacher1
    And I set the following fields to these values:
      | Draggable item 4 | Object |
    And I press "id_submitbutton"
    Then  I should see "Duplication of draggable items is not allowed. The string \"Object\" is already used in Draggable item 2."
    Given I set the following fields to these values:
      | Draggable item 4 | Dynamic |
    And I press "id_submitbutton"
    Then I should see "Ordering for editing"
