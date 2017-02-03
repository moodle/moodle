@tool @tool_usertours
Feature: Apply tour filters to a tour
  In order to give more directed tours
  As an administrator
  I need to create a user tour with filters applied

  @javascript
  Scenario: Add a tour for a different theme
    Given I log in as "admin"
    And I add a new user tour with:
      | Name                | First tour |
      | Description         | My first tour |
      | Apply to URL match  | /my/% |
      | Tour is enabled     | 1 |
      | Theme               | More |
    And I add steps to the "First tour" tour:
      | targettype                  | Title             | Content |
      | Display in middle of page   | Welcome           | Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful |
    When I am on homepage
    Then I should not see "Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful"

  @javascript
  Scenario: Add a tour for a specific role
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | editor1  |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | editor1  | C1     | editingteacher |
      | teacher1 | C1     | teacher        |
      | student1 | C1     | student        |
    And I log in as "admin"
    And I add a new user tour with:
      | Name                | First tour |
      | Description         | My first tour |
      | Apply to URL match  | /course/view.php% |
      | Tour is enabled     | 1 |
      | Role                | Student,Non-editing teacher |
    And I add steps to the "First tour" tour:
      | targettype                  | Title             | Content |
      | Display in middle of page   | Welcome           | Welcome to your course tour.|
    And I log out
    And I log in as "editor1"
    And I am on site homepage
    When I follow "Course 1"
    Then I should not see "Welcome to your course tour."
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    And I should see "Welcome to your course tour."
    And I click on "End tour" "button"
    And I log out
    And I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I should see "Welcome to your course tour."
