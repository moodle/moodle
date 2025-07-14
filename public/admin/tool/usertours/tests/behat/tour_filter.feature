@tool @tool_usertours
Feature: Apply tour filters to a tour
  In order to give more directed tours
  As an administrator
  I need to create a user tour with filters applied

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
      | targettype                | Title   | id_content                   | Content type   |
      | Display in middle of page | Welcome | Welcome to your course tour. | Manual |
    And I log out
    And I log in as "editor1"
    When I am on "Course 1" course homepage
    Then I should not see "Welcome to your course tour."
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should see "Welcome to your course tour."
    And I click on "Got it" "button"
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
      | targettype                | Title   | id_content                   | Content type   |
      | Display in middle of page | Welcome | Welcome to your course tour. | Manual |
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I wait until the page is ready
    And I should see "Welcome to your course tour."
    When I am on "Course 2" course homepage
    And I wait until the page is ready
    Then I should see "Welcome to your course tour."

  @javascript
  Scenario: Add tour for multiple categories and exclude category
    Given the following "categories" exist:
      | name    | category | idnumber |
      | MainCat | 0        | CAT1     |
      | SubCat  | CAT1     | CAT2     |
      | SubCat2 | CAT1     | CAT3     |
      | MainCat2| 0        | CAT4     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
      | Course 2 | C2        | CAT2     |
      | Course 3 | C3        | CAT3     |
      | Course 4 | C4        | CAT4     |
    And the following "users" exist:
      | username |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
      | student1 | C3     | student |
      | student1 | C4     | student |
    And I log in as "admin"
    And I add a new user tour with:
      | Name               | First tour        |
      | Description        | My first tour     |
      | Apply to URL match | /course/view.php% |
      | Tour is enabled    | 1                 |
      | Category           | MainCat,MainCat2  |
      | Exclude categories   | MainCat / SubCat2 |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                   | Content type   |
      | Display in middle of page | Welcome | Welcome to your course tour. | Manual |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I wait until the page is ready
    Then I should see "Welcome to your course tour."
    And I am on "Course 2" course homepage
    And I wait until the page is ready
    And I should see "Welcome to your course tour."
    And I am on "Course 3" course homepage
    And I wait until the page is ready
    And I should not see "Welcome to your course tour."
    And I am on "Course 4" course homepage
    And I wait until the page is ready
    And I should see "Welcome to your course tour."

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
      | Course format      | Weekly sections   |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                   | Content type   |
      | Display in middle of page | Welcome | Welcome to your course tour. | Manual |
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I wait until the page is ready
    Then I should not see "Welcome to your course tour."
    When I am on "Course 2" course homepage
    And I wait until the page is ready
    Then I should see "Welcome to your course tour."

  @javascript
  Scenario: Add tour for a specific course with all courses filter
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
      | Courses            | All courses       |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                   | Content type   |
      | Display in middle of page | Welcome | Welcome to your course tour. | Manual         |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I should see "Welcome to your course tour."
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
      | Courses            | Selected courses  |
      | Selected courses   | C1                |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                   | Content type   |
      | Display in middle of page | Welcome | Welcome to your course tour. | Manual         |
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I wait until the page is ready
    Then I should see "Welcome to your course tour."
    When I am on "Course 2" course homepage
    And I wait until the page is ready
    Then I should not see "Welcome to your course tour."

  @javascript
  Scenario: Add tour for a excluded course
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
      | Name               | First tour                   |
      | Description        | My first tour                |
      | Apply to URL match | /course/view.php%            |
      | Tour is enabled    | 1                            |
      | Courses            | All courses except selected  |
      | Selected courses   | C1                           |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                   | Content type   |
      | Display in middle of page | Welcome | Welcome to your course tour. | Manual         |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I should not see "Welcome to your course tour."
    When I am on "Course 2" course homepage
    And I wait until the page is ready
    Then I should see "Welcome to your course tour."

  @javascript
  Scenario: Add tours with CSS selectors
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
      | Course 2 | C2        | topics | 1                |
    And the following "activities" exist:
      | activity | course | name            | firstpagetitle | wikimode      | idnumber | intro                  | type    |
      | wiki     | C1     | Test wiki name  | First page     | collaborative |          |                        |         |
      | forum    | C2     | Test forum name |                |               | 001      | Test forum description | general |
    And I log in as "admin"
    And I add a new user tour with:
      | Name               | Wiki tour                |
      | Description        | A tour with both matches |
      | Apply to URL match | /course/view.php%        |
      | Tour is enabled    | 1                        |
      | CSS selector       | .modtype_wiki            |
    And I add steps to the "Wiki tour" tour:
      | targettype                | Title   | id_content               | Content type   |
      | Display in middle of page | Welcome | Welcome to the Wiki tour | Manual |
    And I add a new user tour with:
      | Name               | Forum tour               |
      | Description        | A tour with both matches |
      | Apply to URL match | /course/view.php%        |
      | Tour is enabled    | 1                        |
      | CSS selector       | .modtype_forum           |
    And I add steps to the "Forum tour" tour:
      | targettype                | Title   | id_content                | Content type   |
      | Display in middle of page | Welcome | Welcome to the Forum tour | Manual |
    And I am on "Course 1" course homepage
    Then I should see "Welcome to the Wiki tour"
    And I am on "Course 2" course homepage
    Then I should see "Welcome to the Forum tour"

  @javascript
  Scenario: Check filtering respects the sort order
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And I log in as "admin"
    And I add a new user tour with:
      | Name               | First tour     |
      | Description        | The first tour |
      | Apply to URL match | /my/%          |
      | Tour is enabled    | 1              |
      | CSS selector       | #page-my-index |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content                | Content type   |
      | Display in middle of page | Welcome | Welcome to the First tour | Manual |
    And I add a new user tour with:
      | Name               | Second tour     |
      | Description        | The second tour |
      | Apply to URL match | /my/%           |
      | Tour is enabled    | 0               |
      | CSS selector       | #page-my-index  |
    And I add steps to the "Second tour" tour:
      | targettype                | Title   | id_content                 | Content type   |
      | Display in middle of page | Welcome | Welcome to the Second tour | Manual |
    And I add a new user tour with:
      | Name               | Third tour     |
      | Description        | The third tour |
      | Apply to URL match | /my/%           |
      | Tour is enabled    | 1               |
      | CSS selector       | #page-my-index  |
    And I add steps to the "Third tour" tour:
      | targettype                | Title   | id_content                | Content type   |
      | Display in middle of page | Welcome | Welcome to the Third tour | Manual |
    And I am on homepage
    Then I should see "Welcome to the First tour"
    And I open the User tour settings page
    And I click on "Move tour down" "link" in the "The first tour" "table_row"
    And I click on "Move tour down" "link" in the "The first tour" "table_row"
    And I am on homepage
    Then I should see "Welcome to the Third tour"

  @javascript
  Scenario: Show or hide the Exclude Categories option if the selected categories have no child categories
    Given I log in as "admin"
    And I open the User tour settings page
    When I click on "Create a new tour" "link"
    Then "Exclude categories" "select" should be visible
    And I should see "Category 1" in the "Exclude categories" "select"
    And I select "Category 1" from the "Category" singleselect
    And "Exclude categories" "select" should not be visible
    And I select "All" from the "Category" singleselect
    And I should see "Category 1" in the "Exclude categories" "select"
