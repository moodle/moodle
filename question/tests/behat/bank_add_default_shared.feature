@core @core_question
Feature: Add a default question bank
  In order to manage shared questions
  As a teacher
  I need to create a default question bank

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Add a default question bank to a course
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I navigate to "Question banks" in current page administration
    Then I should see "This course doesn't have any question banks yet."
    And I should see "Add"
    And I click on "Create default question bank" "button"
    But I should not see "This course doesn't have any question banks yet."
    And I should see "Default question bank created."
    And I should see "Course 1 course question bank"
