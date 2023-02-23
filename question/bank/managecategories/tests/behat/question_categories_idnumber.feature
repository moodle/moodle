@qbank @qbank_managecategories
Feature: A teacher can put questions with idnumbers in categories with idnumbers in the question bank
  In order to organize my questions
  As a teacher
  I create and edit categories (now with idnumbers)

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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  Scenario: A new question category can only be created with a unique idnumber for a context
    # Note need to create the top category each time.
    When the following "question categories" exist:
      | contextlevel | reference | questioncategory | name           | idnumber |
      | Course       | C1        | Top              | top            |          |
      | Course       | C1        | top              | Used category  | c1used   |
    And I am on the "Course 1" "core_question > course question categories" page
    And I follow "Add category"
    And I set the following fields to these values:
      | Name            | Sub used category |
      | Parent category | Used category     |
      | Category info   | Created as a test |
      | ID number       | c1used            |
    And I press "submitbutton"
    # Standard warning.
    Then I should see "This ID number is already in use"
    # Correction to a unique idnumber for the context.
    And I set the field "ID number" to "c1unused"
    And I press "submitbutton"
    Then I should see "Sub used category"
    And I should see "ID number"
    And I should see "c1unused"
    And I should see "(0)"
    And I should see "Created as a test" in the "Sub used category" "list_item"

  Scenario: A question category can be edited and saved without changing the idnumber
    When the following "question categories" exist:
      | contextlevel | reference | questioncategory | name           | idnumber |
      | Course       | C1        | Top              | top            |          |
      | Course       | C1        | top              | Used category  | c1used   |
    And I am on the "Course 1" "core_question > course question categories" page
    And I click on "Edit this category" "link" in the "Used category" "list_item"
    And I press "Save changes"
    Then I should not see "This ID number is already in use"
