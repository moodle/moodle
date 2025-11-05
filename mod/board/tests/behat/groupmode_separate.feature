@mod @mod_board @javascript
Feature: Usage of mod_board in separate groups mode

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

  Scenario: Nobody may post for all participants in mod_board in separate groups mode
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 1                      |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When the following fields match these values:
      | Separate groups | All participants |
    Then "Add new post to column First Column" "mod_board > button" should not be visible

    When I select "Group A" from the "Separate groups" singleselect
    And "Add new post to column First Column" "mod_board > button" should be visible
    And I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Title Teacher for GA   |
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then I should see "Title Teacher for GA" in the "1" "mod_board > column"

    When I select "Group B" from the "Separate groups" singleselect
    And "Add new post to column First Column" "mod_board > button" should be visible
    And I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Title Teacher for GB   |
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then I should see "Title Teacher for GB" in the "1" "mod_board > column"
    And I should not see "Title Teacher for GA" in the "1" "mod_board > column"

    When I select "All participants" from the "Separate groups" singleselect
    Then I should see "Title Teacher for GA" in the "1" "mod_board > column"
    And I should see "Title Teacher for GB" in the "1" "mod_board > column"

    When I am on the "Sample board" "board activity" page logged in as "student1"
    And I should see "Separate groups: Group A"
    And I should see "Title Teacher for GA" in the "1" "mod_board > column"
    And I should not see "Title Teacher for GB" in the "1" "mod_board > column"
    And "Add new post to column First Column" "mod_board > button" should be visible
    And I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Title Student1 for GA   |
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then I should see "Title Teacher for GA" in the "1" "mod_board > column"
    And I should see "Title Student1 for GA" in the "1" "mod_board > column"
    And I should not see "Title Teacher for GB" in the "1" "mod_board > column"

    And I am on homepage
