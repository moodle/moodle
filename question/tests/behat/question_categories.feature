@core @core_question
Feature: A teacher can put questions in categories in the question bank
  In order to organize their questions
  As a teacher
  I need to put questions in categories

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    # Add 2 test categories.
    And I follow "Question bank"
    And I follow "Categories"
    And I set the following fields to these values:
      | Name | New Category 1 |
      | Parent category | Top |
    And I press "id_submitbutton"
    And I set the following fields to these values:
      | Name | New Category 2 |
      | Parent category | Top |
    And I press "id_submitbutton"
    # Add test question.
    And I add a "Essay" question filling the form with:
      | Question name | my test question |
      | Question text | my test question |
      | Category      | New Category 1  |

  @javascript
  Scenario: Move a question between categories via the question page
    When I set the field "category" to "New Category 1 (1)"
    And I click on "my test question" "checkbox" in the "my test question" "table_row"
    And I set the field "menucategory" to "New Category 2"
    And I press "Move to >>"
    Then I should see "my test question"
    And the "category" select box should contain "New Category 2 (1)"
    And the "category" select box should contain "New Category 1"
    And the "category" select box should not contain "New Category 1 (1)"

  @javascript
  Scenario: Move a question between categories via the question settings page
    When I click on "Edit" "link" in the "my test question" "table_row"
    And I click on "Use this category" "checkbox"
    And I set the field "Save in category" to "New Category 2"
    And I press "id_submitbutton"
    Then I should see "my test question"
    And the "category" select box should contain "New Category 2 (1)"
    And the "category" select box should not contain "New Category 1 (1)"

  @javascript
  Scenario: Delete a question category
    When I follow "Categories"
    And I click on "Delete" "link" in the "//a[text()='New Category 1']/parent::b/parent::li" "xpath_element"
    Then I should see "The category 'New Category 1' contains 1 questions"
    And I press "Save in category"
    And I should not see "New Category 1"
