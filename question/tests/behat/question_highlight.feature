@core @core_question
Feature: A teacher can see highlighted questions in the question bank
  In order to see my edited questions
  As a teacher
  I need to be able see the highlight of my edited question.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And 101 "questions" exist with the following data:
      | questioncategory | Test questions                |
      | qtype            | essay                         |
      | name             | essay [count]                 |
      | questiontext     | Write about whatever you want |

  Scenario: Edited question highlight is retained when go to multiple pages.
    Given I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"
    And I am on the "essay 1" "core_question > edit" page logged in as "teacher1"
    And I set the following fields to these values:
      | Question name | essay 1 edited |
    And I press "id_submitbutton"
    And I should see "essay 1 edited"
    And ".highlight" "css_element" should exist in the "#categoryquestions" "css_element"
    When I click on "Show all 101" "link"
    Then ".highlight" "css_element" should exist in the "#categoryquestions" "css_element"
