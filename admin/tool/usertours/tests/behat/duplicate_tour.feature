@tool @tool_usertours
Feature: Duplicate a user tour
  As an administrator
  I want to duplicate a user tour

  @javascript
  Scenario: Tour can be duplicated
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And I log in as "admin"
    And I add a new user tour with:
      | Name                | First tour |
      | Description         | My first tour |
      | Apply to URL match  | /my/% |
      | Tour is enabled     | 0 |
    And I add steps to the "First tour" tour:
      | targettype                  | Title             | Content |
      | Display in middle of page   | Welcome           | Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful |
    And I open the User tour settings page
    And I should see "1" occurrences of "First tour" in the "admintable" "table"
    And I click on "Duplicate" "link" in the "My first tour" "table_row"
    And I open the User tour settings page
    Then I should see "1" occurrences of "First tour (copy)" in the "admintable" "table"
