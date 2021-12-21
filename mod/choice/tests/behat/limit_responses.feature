@mod @mod_choice
Feature: Limit choice responses
  In order to restrict students from selecting a response more than a specified number of times
  As a teacher
  I need to limit the choice responses

  Scenario: Limit the number of responses allowed for a choice activity and verify the result as students
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "activities" exist:
      | activity | name        | intro              | course | idnumber | option             | showavailable | limitanswers |
      | choice   | Choice name | Choice description | C1     | choice1  | Option 1, Option 2 | 1             | 1            |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "Settings" in current page administration
    And I set the field "Limit 1" to "1"
    And I press "Save and display"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I choose "Option 1" from "Choice name" choice activity
    Then I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I should see "Option 1 (Full)"
    And I should see "Responses: 1"
    And I should see "Limit: 1"
    And the "choice_1" "radio" should be disabled
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Limit the number of responses allowed | No |
    And I press "Save and return to course"
    And I log out
    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    Then I should not see "Limit: 1"
    And the "choice_1" "radio" should be enabled
