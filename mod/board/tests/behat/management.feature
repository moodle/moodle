@mod @mod_board @javascript
Feature: Basic mod_board management tasks

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | First     | Student  | student1@example.com |
      | teacher1 | First     | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  Scenario: Create and update mod_board instance
    Given I am on the "Course 1" course page logged in as "teacher1"
    And I turn editing mode on

    When I open dialog for adding mod_board to "General" section
    And the following fields match these values:
      | Rating posts                                              | Disabled               |
      | Hide column headers from students                         | 0                      |
      | Sort by                                                   | None                   |
      | Single user mode                                          | Disabled               |
      | Limit students posting by date                            | 0                      |
      | Allow all users to edit the placement of their own posts. | 0                      |
      | Enable blank target                                       | 0                      |
      | Embed the board into the course page                      | 0                      |
      | Hide embedded board name (needed on some themes)          | 0                      |
    And I set the following fields to these values:
      | Name        | My test board 1        |
      | Description | Such a nice test board |
    And I press "Save and return to course"
    And I click on "My test board 1" "link" in the "General" "section"
    Then I should see "Such a nice test board"

    And I am on the "Course 1" course page
    When I open "My test board 1" actions menu
    And I click on "Edit settings" "link" in the "My test board 1" activity
    And the following fields match these values:
      | Name                                                      | My test board 1        |
      | Description                                               | Such a nice test board |
      | Rating posts                                              | Disabled               |
      | Hide column headers from students                         | 0                      |
      | Sort by                                                   | None                   |
      | Single user mode                                          | Disabled               |
      | Limit students posting by date                            | 0                      |
      | Allow all users to edit the placement of their own posts. | 0                      |
      | Enable blank target                                       | 0                      |
      | Embed the board into the course page                      | 0                      |
      | Hide embedded board name (needed on some themes)          | 0                      |
    And I set the following fields to these values:
      | Name                                                      | My test board X        |
      | Description                                               | Such a bad test board  |
      | Rating posts                                              | by All                 |
      | Hide column headers from students                         | 1                      |
      | Sort by                                                   | Creation date          |
      | Single user mode                                          | Single user mode (private) |
      | Allow all users to edit the placement of their own posts. | 1                      |
      | Enable blank target                                       | 1                      |
    And I press "Save and return to course"
    And I open "My test board X" actions menu
    And I click on "Edit settings" "link" in the "My test board X" activity
    Then the following fields match these values:
      | Name                                                      | My test board X        |
      | Description                                               | Such a bad test board  |
      | Rating posts                                              | by All                 |
      | Hide column headers from students                         | 1                      |
      | Sort by                                                   | Creation date          |
      | Single user mode                                          | Single user mode (private) |
      | Allow all users to edit the placement of their own posts. | 1                      |
      | Enable blank target                                       | 1                      |
    And I press "Cancel"

  Scenario: Add, edit, move and delete columns in mod_board
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I click on "Close course index" "button"
    And I should see "Heading" in the "1" "mod_board > column"
    And I should see "Heading" in the "2" "mod_board > column"
    And I should see "Heading" in the "3" "mod_board > column"
    And "4" "mod_board > column" should not exist
    And "5" "mod_board > column" should not exist

    When I click on "Add new column" "mod_board > button"
    And I set the following fields to these values:
      | Name | Fourth Column |
    And I click on "Submit" "button" in the "Add new column" "dialogue"
    Then I should see "Heading" in the "1" "mod_board > column"
    And I should see "Heading" in the "2" "mod_board > column"
    And I should see "Heading" in the "3" "mod_board > column"
    And I should see "Fourth Column" in the "4" "mod_board > column"
    And "5" "mod_board > column" should not exist
    And I reload the page
    And I should see "Heading" in the "1" "mod_board > column"
    And I should see "Heading" in the "2" "mod_board > column"
    And I should see "Heading" in the "3" "mod_board > column"
    And I should see "Fourth Column" in the "4" "mod_board > column"
    And "5" "mod_board > column" should not exist

    When I click on "Update column Heading" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Name | First Column |
    And I click on "Update" "button" in the "Update column Heading" "dialogue"
    When I click on "Update column Heading" "mod_board > button" in the "2" "mod_board > column"
    And I set the following fields to these values:
      | Name | Second Column |
    And I click on "Update" "button" in the "Update column Heading" "dialogue"
    When I click on "Update column Heading" "mod_board > button" in the "3" "mod_board > column"
    And I set the following fields to these values:
      | Name | Third Column |
    And I click on "Update" "button" in the "Update column Heading" "dialogue"
    Then I should see "First Column" in the "1" "mod_board > column"
    And I should see "Second Column" in the "2" "mod_board > column"
    And I should see "Third Column" in the "3" "mod_board > column"
    And I should see "Fourth Column" in the "4" "mod_board > column"
    And "5" "mod_board > column" should not exist
    And I reload the page
    And I should see "First Column" in the "1" "mod_board > column"
    And I should see "Second Column" in the "2" "mod_board > column"
    And I should see "Third Column" in the "3" "mod_board > column"
    And I should see "Fourth Column" in the "4" "mod_board > column"
    And "5" "mod_board > column" should not exist

    When I click on "Add new post to column Third Column" "mod_board > button" in the "3" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Title 3x1   |
      | Content    | Content 3x1 |
    And I click on "Post" "button" in the "New post for column Third Column" "dialogue"
    Then I should see "Title 3x1" in the "3" "mod_board > column"
    And I should see "Content 3x1" in the "3" "mod_board > column"

    When I click on "Move column Third Column" "mod_board > button" in the "3" "mod_board > column"
    And I click on "Move column to first place" "link"
    # XPath position does not change on move, we need to force reload
    And I reload the page
    Then I should see "Third Column" in the "1" "mod_board > column"
    And I should see "First Column" in the "2" "mod_board > column"
    And I should see "Second Column" in the "3" "mod_board > column"
    And I should see "Fourth Column" in the "4" "mod_board > column"

    When I click on "Move column Third Column" "mod_board > button" in the "1" "mod_board > column"
    And I click on "Move column after column Second Column" "link"
    # XPath position does not change on move, we need to force reload
    And I reload the page
    Then I should see "First Column" in the "1" "mod_board > column"
    And I should see "Second Column" in the "2" "mod_board > column"
    And I should see "Third Column" in the "3" "mod_board > column"
    And I should see "Fourth Column" in the "4" "mod_board > column"

    When I click on "Column Third Column unlocked" "mod_board > button" in the "3" "mod_board > column"
    Then "Add new post to column Third Column" "mod_board > button" should not be visible
    When I click on "Column Third Column locked" "mod_board > button" in the "3" "mod_board > column"
    Then "Add new post to column Third Column" "mod_board > button" should be visible

    When I click on "Delete column Third Column" "mod_board > button" in the "3" "mod_board > column"
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    Then I should see "First Column" in the "1" "mod_board > column"
    And I should see "Second Column" in the "2" "mod_board > column"
    And I should see "Fourth Column" in the "3" "mod_board > column"
    And "4" "mod_board > column" should not exist
    And I reload the page
    And I should see "First Column" in the "1" "mod_board > column"
    And I should see "Second Column" in the "2" "mod_board > column"
    And I should see "Fourth Column" in the "3" "mod_board > column"
    And "4" "mod_board > column" should not exist

    And I am on homepage
