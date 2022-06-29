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

  Scenario: Enable the choice include inactive option and check that responses from inactive students are visible
    Given the following "activity" exists:
      | activity        | choice                       |
      | course          | C1                           |
      | idnumber        | choice1                      |
      | name            | Choice name                  |
      | intro           | Choice Description           |
      | section         | 1                            |
      | option          | Option 1, Option 2, Option 3 |
      | includeinactive | 1                            |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose "Option 1" from "Choice name" choice activity
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I choose "Option 2" from "Choice name" choice activity
    And I log out
    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I choose "Option 3" from "Choice name" choice activity
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | status |
      | student1 | C1 | student | 1 |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Choice name" "link" in the "region-main" "region"
    And I navigate to "Responses" in current page administration
    Then I should see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | timestart |
      | student2 | C1 | student | 2145830400 |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Choice name" "link" in the "region-main" "region"
    Then I navigate to "Responses" in current page administration
    And I should see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | timeend |
      | student3 | C1 | student | 1425168000 |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Choice name" "link" in the "region-main" "region"
    Then I navigate to "Responses" in current page administration
    And I should see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I log out

  Scenario: Disable the choice include inactive option and check that responses from inactive students are not visible
    Given the following "activity" exists:
      | activity        | choice                       |
      | course          | C1                           |
      | idnumber        | choice1                      |
      | name            | Choice name                  |
      | intro           | Choice Description           |
      | section         | 1                            |
      | option          | Option 1, Option 2, Option 3 |
      | includeinactive | 0                            |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose "Option 1" from "Choice name" choice activity
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I choose "Option 2" from "Choice name" choice activity
    And I log out
    And I log in as "student3"
    And I am on "Course 1" course homepage
    And I choose "Option 3" from "Choice name" choice activity
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | status |
      | student1 | C1 | student | 1 |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Choice name" "link" in the "region-main" "region"
    Then I navigate to "Responses" in current page administration
    And I should not see "Student 1"
    And I should see "Student 2"
    And I should see "Student 3"
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | timestart |
      | student2 | C1 | student | 2145830400 |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Choice name" "link" in the "region-main" "region"
    Then I navigate to "Responses" in current page administration
    And I should not see "Student 1"
    And I should not see "Student 2"
    And I should see "Student 3"
    And I log out
    And the following "course enrolments" exist:
      | user | course | role | timeend |
      | student3 | C1 | student | 1425168000 |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Choice name" "link" in the "region-main" "region"
    Then I navigate to "Responses" in current page administration
    And I should not see "Student 1"
    And I should not see "Student 2"
    And I should not see "Student 3"
    And I log out
