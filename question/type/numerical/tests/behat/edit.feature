@qtype @qtype_numerical
Feature: Test editing a Numerical question
  As a teacher
  In order to be able to update my Numerical question
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
      | questioncategory | qtype     | name                  | template |
      | Test questions   | numerical | Numerical for editing | pi |
    And the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  Scenario: Edit a Numerical question
    When I choose "Edit question" action for "Numerical for editing" in the question bank
    Then the field "id_answer_0" matches value "3#14"
    When I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    Then I should see "You must supply a value here."
    When I set the following fields to these values:
      | Question name | Edited Numerical name |
    And I press "id_submitbutton"
    Then I should see "Edited Numerical name"
    When I choose "Edit question" action for "Edited Numerical name" in the question bank
    And I set the following fields to these values:
      | id_answer_1    | 3#141592 |
      | id_tolerance_1 | 0#005    |
      | id_answer_2    | 3.05     |
      | id_tolerance_2 | 0.005    |
      | id_answer_3    | 3,01     |
    And I press "id_submitbutton"
    Then I should see "Edited Numerical name"
