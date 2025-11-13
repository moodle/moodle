@block @block_myoverview @javascript
Feature: The my overview block allows users to easily sort their courses
  In order to sort course in the my overview block
  As a user
  I can choose from a selection of sorting options

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | X        | student1@example.com | S1       |
    And the following "categories" exist:
      | name       | category | idnumber |
      | Category 1 | 0        | CAT1     |
    And the following "courses" exist:
      | fullname | shortname | category | startdate                   | enddate                    |
      | Course 1 | C1        | 0        | ##1 month ago##             | ##15 days ago##            |
      | Course 2 | C0        | 0        | ##yesterday##               | ##tomorrow##               |
      | Course 3 | C3        | 0        | ##2 month ago##             | ##tomorrow##               |
      | Course 4 | C4        | CAT1     | ##yesterday##               | ##tomorrow##               |
      | Course 5 | C5        | 0        | ##first day of next month## | ##last day of next month## |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C0     | student |
      | student1 | C3     | student |
      | student1 | C4     | student |
      | student1 | C5     | student |
    And the following config values are set as admin:
      | courselistshortnames | 1 |

  Scenario Outline: Check the function of available course sorting options
    Given I am on the "Course 5" course page logged in as "student1"
    When I am on the "My courses" page
    And I click on "sortingdropdown" "button" in the "Course overview" "block"
    And I click on "<dropdownstring>" "link" in the "Course overview" "block"
    Then "<coursebefore>" "text" should appear before "<courseafter>" "text" in the "Course overview" "block"

    Examples:
      | dropdownstring        | coursebefore | courseafter |
      | Sort by course name   | Course 1     | Course 2    |
      | Sort by short name    | Course 2     | Course 1    |
      | Sort by last accessed | Course 5     | Course 1    |
      | Sort by start date    | Course 3     | Course 1    |
