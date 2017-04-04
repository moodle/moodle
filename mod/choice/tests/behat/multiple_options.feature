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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | Allow more than one choice to be selected | Yes |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
    And I log out
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | Allow more than one choice to be selected | Yes |
      | Limit the number of responses allowed | 1 |
      | option[0] | Option 1 |
      | limit[0] | 1 |
      | option[1] | Option 2 |
      | limit[1] | 1 |
      | option[2] | Option 3 |
      | limit[2] | 1 |
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
