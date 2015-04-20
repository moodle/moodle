@mod @mod_choice
Feature: Include responses from inactive users
  In order to view responses from inactive or suspended users in choice results
  As a teacher
  I need to enable the choice include inactive option

  Background:
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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

  @javascript
  Scenario: Enable the choice include inactive option and check that responses from inactive students are visible
    Given I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
      | option[2] | Option 3 |
      | Include responses from inactive/suspended users | Yes |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I choose "Option 1" from "Choice name" choice activity
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I choose "Option 2" from "Choice name" choice activity
    And I log out
    And I log in as "student3"
    And I follow "Course 1"
    And I choose "Option 3" from "Choice name" choice activity
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | status |
      | student1 | C1 | student | 1 |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Choice name"
    Then I should see "View 3 responses"
    And I follow "View 3 responses"
    And I should see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | timestart |
      | student2 | C1 | student | 2145830400 |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Choice name"
    Then I should see "View 3 responses"
    And I follow "View 3 responses"
    And I should see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | timeend |
      | student3 | C1 | student | 1425168000 |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Choice name"
    Then I should see "View 3 responses"
    And I follow "View 3 responses"
    And I should see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I log out

  @javascript
  Scenario: Disable the choice include inactive option and check that responses from inactive students are not visible
    Given I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
      | option[2] | Option 3 |
      | Include responses from inactive/suspended users | No |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I choose "Option 1" from "Choice name" choice activity
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I choose "Option 2" from "Choice name" choice activity
    And I log out
    And I log in as "student3"
    And I follow "Course 1"
    And I choose "Option 3" from "Choice name" choice activity
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | status |
      | student1 | C1 | student | 1 |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Choice name"
    Then I should see "View 2 responses"
    And I follow "View 2 responses"
    And I should not see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | timestart |
      | student2 | C1 | student | 2145830400 |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Choice name"
    Then I should see "View 1 responses"
    And I follow "View 1 responses"
    And I should not see "Student 1"
    And I should not see "Student 2"
    And I should see "Student 3"
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | timeend |
      | student3 | C1 | student | 1425168000 |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Choice name"
    Then I should see "View 0 responses"
    And I follow "View 0 responses"
    And I should not see "Student 1"
    And I should not see "Student 2"
    And I should not see "Student 3"
    And I log out
