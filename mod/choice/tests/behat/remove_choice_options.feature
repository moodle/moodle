@mod @mod_choice
Feature: Update a choice activity removing options
  In order to remove incorrect or unwanted options
  As a teacher
  I need to update the choice activity

  Scenario: Update a choice activity that has student responses.
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
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
      | option[2] | Option 3 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I choose "Option 3" from "Choice name" choice activity
    And I should see "Your selection: Option 3"
    And I should see "Your choice has been saved"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I choose "Option 2" from "Choice name" choice activity
    And I should see "Your selection: Option 2"
    And I should see "Your choice has been saved"
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Choice name"
    And I should see "View 2 responses"
    And I navigate to "Edit settings" node in "Choice administration"
    And I set the field "option[2]" to ""
    And I press "Save and display"
    Then I should see "View 1 responses"
