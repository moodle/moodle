@mod @mod_board @javascript
Feature: Usage of mod_board with disabled single user mode

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | First     | Student  | student1@example.com |
      | student2 | Second    | Student  | student2@example.com |
      | student3 | Third     | Student  | student3@example.com |
      | teacher1 | First     | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group A | C1     | GA       |
      | Group B | C1     | GB       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "group members" exist:
      | user     | group |
      | student1 | GA    |
      | student2 | GB    |

  Scenario: Students may post and read in mod_board when single user mode disabled
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Title Teacher 1-1 |
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then I should see "Title Teacher 1-1" in the "1" "mod_board > column"

    When I am on the "Sample board" "board activity" page logged in as "student1"
    And I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Title Student 1-1 |
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then I should see "Title Teacher 1-1" in the "1" "mod_board > column"
    And I should see "Title Student 1-1" in the "1" "mod_board > column"

    When I am on the "Sample board" "board activity" page logged in as "student2"
    And I click on "Add new post to column Second Column" "mod_board > button" in the "2" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Title Student 2-1 |
    And I click on "Post" "button" in the "New post for column Second Column" "dialogue"
    Then I should see "Title Teacher 1-1" in the "1" "mod_board > column"
    And I should see "Title Student 1-1" in the "1" "mod_board > column"
    And I should see "Title Student 2-1" in the "2" "mod_board > column"

    And I am on homepage

  Scenario: Admin may login-as to student account and post in mod_board when single user mode disabled
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     |
      | Sample board | 1           | Heading T1  |             | teacher1 |
      | Sample board | 1           | Heading S1  |             | student1 |
    When I am on the "student1" "user > profile" page logged in as "admin"
    And I follow "Log in as"
    And I should see "You are logged in as First Student"
    And I press "Continue"
    And I am on the "My courses" page
    And I follow "Course 1"
    And I follow "Sample board"
    And I should see "Heading T1"
    And I should see "Heading S1"
    And I click on "Add new post to column Heading" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Heading X1   |
    And I click on "Post" "button" in the "New post for column Heading" "dialogue"
    Then I should see "Heading T1"
    And I should see "Heading S1"
    And I should see "Heading X1"

    And I am on homepage
