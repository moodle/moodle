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
    When I am on "Course 1" course homepage
    Then I should not see "Welcome to your course tour."
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "Welcome to your course tour."
    And I click on "End tour" "button"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I should see "Welcome to your course tour."

  @javascript
  Scenario: Add tour for a specific category and its subcategory
    Given the following "categories" exist:
      | name    | category | idnumber |
      | MainCat | 0        | CAT1     |
      | SubCat  | CAT1     | CAT2     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
      | Course 2 | C2        | CAT2     |
    And the following "users" exist:
      | username |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
    And I log in as "admin"
    And I add a new user tour with:
      | Name               | First tour        |
      | Description        | My first tour     |
      | Apply to URL match | /course/view.php% |
      | Tour is enabled    | 1                 |
      | Category           | MainCat           |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | Content                     |
      | Display in middle of page | Welcome | Welcome to your course tour.|
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I wait until the page is ready
    Then I should see "Welcome to your course tour."
    When I am on "Course 2" course homepage
    And I wait until the page is ready
    Then I should see "Welcome to your course tour."

  @javascript
  Scenario: Add tour for a specific courseformat
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
      | Course 2 | C2        | weeks  |
    And the following "users" exist:
      | username |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
    And I log in as "admin"
    And I add a new user tour with:
      | Name               | First tour        |
      | Description        | My first tour     |
      | Apply to URL match | /course/view.php% |
      | Tour is enabled    | 1                 |
      | Course format      | Weekly format     |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | Content                     |
      | Display in middle of page | Welcome | Welcome to your course tour.|
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I wait until the page is ready
    Then I should not see "Welcome to your course tour."
    When I am on "Course 2" course homepage
    And I wait until the page is ready
    Then I should see "Welcome to your course tour."

  @javascript
  Scenario: Add tour for a specific course
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
      | Course 2 | C2        | weeks  |
    And the following "users" exist:
      | username |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
    And I log in as "admin"
    And I add a new user tour with:
      | Name               | First tour        |
      | Description        | My first tour     |
      | Apply to URL match | /course/view.php% |
      | Tour is enabled    | 1                 |
      | Courses            | C1                |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | Content                     |
      | Display in middle of page | Welcome | Welcome to your course tour.|
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I wait until the page is ready
    Then I should see "Welcome to your course tour."
    When I am on "Course 2" course homepage
    And I wait until the page is ready
    Then I should not see "Welcome to your course tour."

  @javascript
  Scenario: Aria tags should not exist
    Given I log in as "admin"
    And I open the User tour settings page
    # Turn on default tour for boost theme.
    And I click on "Enable" "link" in the "Boost - administrator" "table_row"
    And I am on site homepage
    When I click on "Next" "button"
    Then "button[aria-describedby^='tour-step-tool_usertours']" "css_element" should exist
    And "button[tabindex]" "css_element" should exist
    When I click on "Next" "button"
    Then "button[aria-describedby^='tour-step-tool_usertours']" "css_element" should not exist
    And "button[tabindex]" "css_element" should not exist
    When I click on "Previous" "button"
    Then "button[aria-describedby^='tour-step-tool_usertours']" "css_element" should exist
    And "button[tabindex]" "css_element" should exist
    When I click on "End tour" "button"
    Then "button[aria-describedby^='tour-step-tool_usertours']" "css_element" should not exist
    And "button[tabindex]" "css_element" should not exist
