@core @core_course @javascript
Feature: Users can request and approve courses
  As a moodle admin
  In order to improve course creation process
  I need to be able to enable course approval

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | 1 | user1@example.com |
      | user2 | User | 2 | user2@example.com |
      | user3 | User | 3 | user3@example.com |

  Scenario: Simple course request workflow
    Given the following "system role assigns" exist:
      | user  | course               | role    |
      | user2 | Acceptance test site | manager |
    And the following config values are set as admin:
      | lockrequestcategory | 1 |
    And the following "role capability" exists:
      | role                  | user  |
      | moodle/course:request | allow |
    When I log in as "user1"
    And I am on course index
    And I click on "More actions" "button"
    And I click on "Request a course" "link"
    And I set the following fields to these values:
      | Course full name  | My new course |
      | Course short name | Mynewcourse   |
      | Supporting information | pretty please |
    And I press "Request a course"
    And I should see "Course request submitted."
    And I press "Continue"
    And I am on course index
    And I should not see "My new course"
    And I log out
    And I log in as "user2"
    And I am on course index
    And I click on "More actions" "button"
    And I click on "Courses pending approval" "link"
    And the following should exist in the "pendingcourserequests" table:
      | Requested by | Course short name | Course full name | Category   | Reason for course request |
      | User 1       | Mynewcourse       | My new course    | Category 1 | pretty please             |
    And I click on "Approve" "button" in the "My new course" "table_row"
    And I press "Save and return"
    And I should see "There are no courses pending approval"
    And I press "Back to course listing"
    And I should see "My new course"
    And I log out
    And I log in as "user1"
    And I am on course index
    And I follow "My new course"
    And I navigate to course participants
    And I should see "Teacher" in the "User 1" "table_row"
    And I log out

  Scenario: Course request with category selection
    Given the following "categories" exist:
      | name             | category | idnumber |
      | Science category | 0        | SCI |
      | English category | 0        | ENG |
      | Other category   | 0        | MISC |
    Given the following "roles" exist:
      | name             | shortname       | description      | archetype      |
      | Course requestor | courserequestor | My custom role 1 |                |
    And the following "role assigns" exist:
      | user  | role            | contextlevel | reference |
      | user1 | courserequestor | Category     | SCI       |
      | user1 | courserequestor | Category     | ENG       |
      | user2 | manager         | Category     | SCI       |
      | user3 | manager         | Category     | ENG       |
    And the following "role capability" exists:
      | role                  | courserequestor |
      | moodle/course:request | allow           |
    And I log in as "user1"
    And I am on course index
    And I follow "English category"
    And I click on "More actions" "button"
    And I click on "Request a course" "link"
    And I should see "English category" in the ".form-autocomplete-selection" "css_element"
    And I set the following fields to these values:
      | Course full name  | My new course |
      | Course short name | Mynewcourse   |
      | Supporting information | pretty please |
    And I press "Request a course"
    And I log out
    And I log in as "user2"
    And I am on course index
    And I follow "English category"
    And I should not see "More" in the "region-main" "region"
    And I should not see "Courses pending approval"
    And I am on course index
    And I follow "Science category"
    And I click on "More actions" "button"
    And I click on "Courses pending approval" "link"
    And I should not see "Mynewcourse"
    And I press "Back to course listing"
    And I log out
    And I log in as "user3"
    And I am on course index
    And I follow "English category"
    And I click on "More actions" "button"
    And I click on "Courses pending approval" "link"
    And the following should exist in the "pendingcourserequests" table:
      | Requested by | Course short name | Course full name | Category         | Reason for course request |
      | User 1       | Mynewcourse       | My new course    | English category | pretty please             |
    And I click on "Approve" "button" in the "Mynewcourse" "table_row"
    And I press "Save and return"
    And I am on course index
    And I follow "English category"
    And I should see "My new course"
    And I log out
