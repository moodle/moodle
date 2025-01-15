@core @core_question
Feature: Manage question banks
  In order to manage shared questions
  As a teacher
  I need to create and update a question bank

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Steve1    | Student1 | student1@example.com |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity | name   | course | section | intro        | showdescription |
      | qbank    | bank1  | C1     | 0       | Bank 1 intro | 0               |
      | qbank    | bank2  | C1     | 0       | Bank 2 intro | 0               |

  @javascript
  Scenario: Show description when show description checkbox ticked
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I navigate to "Question banks" in current page administration
    And I open the action menu in "bank2" "list_item"
    And I choose "Edit settings" in the open action menu
    And I set the field "Display description on manage question banks page" to "1"
    And I press "Save and return to question bank list"
    Then I should see "Bank 2 intro"
    And I should see "bank1"
    But I should not see "Bank 1 intro"

  @javascript
  Scenario: Update a question bank
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I navigate to "Question banks" in current page administration
    And I open the action menu in "bank1" "list_item"
    And I choose "Edit settings" in the open action menu
    And I set the following fields to these values:
    | Question bank name                                | Bank 1 updated       |
    | Display description on manage question banks page | 1                    |
    | Description                                       | Bank 1 intro updated |
    And I press "Save and return to question bank list"
    Then I should see "Bank 1 updated"
    And I should see "Bank 1 intro updated"

  @javascript
  Scenario: Delete a question bank
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I navigate to "Question banks" in current page administration
    And I open the action menu in "bank1" "list_item"
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button"
    Then I should not see "bank1"
    But I should see "bank2"
