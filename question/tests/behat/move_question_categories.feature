@core @core_question
Feature: A teacher can move question categories in the question bank
  In order to organize my questions
  As a teacher
  I create question categories and move them in the question bank

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
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  Scenario: A question category can be moved to another context
    When I follow "Test quiz"
    And I navigate to "Question bank > Categories" in current page administration
    And I set the following fields to these values:
      | Name            | Test category         |
      | Parent category | Top for Test quiz     |
    And I press "submitbutton"
    And I click on "Share in context for Course: Course 1" "link" in the "Test category" "list_item"
    Then I should see "Test category" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' questioncategories ') and contains(concat(' ', normalize-space(@class), ' '), ' contextlevel50 ')]" "xpath_element"

  Scenario: A question category can be moved to top level
    When I follow "Test quiz"
    And I navigate to "Question bank > Categories" in current page administration
    And I set the following fields to these values:
      | Name            | Test category         |
      | Parent category | Default for Test quiz |
      | Category info   | Created as a test     |
    And I press "submitbutton"
    And I click on "Move to top level" "link" in the "Test category" "list_item"
    Then I should see "Test category" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' questioncategories ') and contains(concat(' ', normalize-space(@class), ' '), ' contextlevel70 ')]" "xpath_element"
    And "//div[contains(concat(' ', normalize-space(@class), ' '), ' questioncategories ') and contains(concat(' ', normalize-space(@class), ' '), ' contextlevel70 ')]//li//ul" "xpath_element" should not exist
