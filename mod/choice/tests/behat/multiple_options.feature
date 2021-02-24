@mod @mod_choice
Feature: Multiple option choice response
  In order to ask questions as a choice of multiple responses
  As a teacher
  I need to add choice activities to courses with multiple options enabled

  Scenario: Complete a choice with multiple options enabled
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exist:
      | activity | name        | intro              | course | idnumber | option                       | allowmultiple |
      | choice   | Choice name | Choice description | C1     | 00001    | Option 1, Option 2, Option 3 | 1             |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose options "Option 1","Option 2" from "Choice name" choice activity
    Then I should see "Your selection: Option 1; Option 2"
    And I should see "Your choice has been saved"

  Scenario: Complete a choice with multiple options enabled and limited responses set
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activity" exist:
      | activity | name        | intro              | course | idnumber | option                       | allowmultiple | showavailable | limitanswers |
      | choice   | Choice name | Choice description | C1     | choice1  | Option 1, Option 2, Option 3 | 1             | 1             | 1            |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Choice name"
    And I click on "Actions menu" "link"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | Limit 1 | 1 |
      | Limit 2 | 1 |
      | Limit 3 | 1 |
    And I press "Save and display"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose options "Option 1","Option 2" from "Choice name" choice activity
    Then I should see "Your selection: Option 1; Option 2"
    And I should see "Your choice has been saved"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I should see "Option 1 (Full)"
    And I should see "Option 2 (Full)"
    And I should see "Option 3"
    And the "#choice_1" "css_element" should be disabled
    And the "#choice_2" "css_element" should be disabled
    And the "#choice_3" "css_element" should be enabled
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I follow "View 1 responses"
    Then I should see "Option 1 (Full)"
    And I should see "Limit: 1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Choice name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Limit the number of responses allowed | No |
    And I press "Save and return to course"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I follow "View 1 responses"
    Then I should not see "Limit: 1"
    And I log out
