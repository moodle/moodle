@block @block_recentlyaccessedcourses @javascript
Feature: The recently accessed courses block allows users to easily access their most recently accessed courses
  In order to access the most recently accessed courses
  As a user
  I can use the Recently accessed courses block in my dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "categories" exist:
      | name        | category | idnumber |
      | Category A  | 0        | CATA     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
      | Course 3 | C3        | 0        |
      | Course 4 | C4        | CATA     |
      | Course 5 | C5        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
      | student1 | C3     | student |
      | student1 | C4     | student |
      | student1 | C5     | student |
    And the following "blocks" exist:
      | blockname               | contextlevel | reference | pagetypepattern | defaultregion |
      | recentlyaccessedcourses | System       | 1         | my-index        | content       |

  Scenario: User has not accessed any course
    Given I log in as "student1"
    Then I should see "No recent courses" in the "Recently accessed courses" "block"

  Scenario: User has accessed two courses
    Given I log in as "student1"
    And I should not see "Course 1" in the "Recently accessed courses" "block"
    And I should not see "Course 2" in the "Recently accessed courses" "block"
    When I am on "Course 1" course homepage
    And I am on "Course 2" course homepage
    And I follow "Dashboard"
    And I change window size to "large"
    Then I should see "Course 1" in the "Recently accessed courses" "block"
    And I should see "Course 2" in the "Recently accessed courses" "block"
    And I should not see "Course 3" in the "Recently accessed courses" "block"
    And I should not see "Course 4" in the "Recently accessed courses" "block"
    And I should not see "Course 5" in the "Recently accessed courses" "block"

  Scenario: Show course category name
    Given the following config values are set as admin:
      | displaycategories | 1 | block_recentlyaccessedcourses |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I am on "Course 4" course homepage
    And I follow "Dashboard"
    And I should see "Category 1" in the "Recently accessed courses" "block"
    And I should see "Category A" in the "Recently accessed courses" "block"

  Scenario: Hide course category name
    Given the following config values are set as admin:
      | displaycategories | 0 | block_recentlyaccessedcourses |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I am on "Course 4" course homepage
    And I follow "Dashboard"
    And I should not see "Category 1" in the "Recently accessed courses" "block"
    And I should not see "Category A" in the "Recently accessed courses" "block"

  Scenario: Show short course name
    Given the following config values are set as admin:
      | courselistshortnames | 1 |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I am on "Course 4" course homepage
    And I follow "Dashboard"
    And I should see "C1" in the "Recently accessed courses" "block"
    And I should see "C4" in the "Recently accessed courses" "block"

  Scenario: Hide short course name
    Given the following config values are set as admin:
      | courselistshortnames | 0 |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I am on "Course 4" course homepage
    And I follow "Dashboard"
    And I should not see "C1" in the "Recently accessed courses" "block"
    And I should not see "C4" in the "Recently accessed courses" "block"
