@mod @mod_board @javascript
Feature: Limited Markdown support in mod_board post content

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
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
    And the following config values are set as admin:
      | media_selection | 2 | mod_board |

  Scenario: Users may use limited Markdown syntax in mod_board post content
    Given I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | My post S1                                          |
    And I set the field "Content" to multiline:
"""
# My heading 1

- list item
- list item

"""
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then "h4" "css_element" should exist in the "My post S1" "mod_board > note"
    And I should see "My heading 1" in the "My post S1" "mod_board > note"
