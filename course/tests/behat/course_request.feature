@core @core_course
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
      | user  | course | role |
      | user2 | Acceptance test site | manager |
    Given I log in as "admin"
    And I set the following administration settings values:
      | lockrequestcategory | 1 |
    And I set the following system permissions of "Authenticated user" role:
      | capability | permission |
      | moodle/course:request | Allow |
    And I log out
    When I log in as "user1"
    And I am on course index
    And I press "Request a course"
    And I set the following fields to these values:
      | Course full name  | My new course |
      | Course short name | Mynewcourse   |
      | Supporting information | pretty please |
    And I press "Request a course"
    And I should see "Your course request has been saved successfully."
    And I press "Continue"
    And I am on course index
    And I should not see "My new course"
    And I log out
    And I log in as "user2"
    And I am on course index
    And I press "Courses pending approval"
    And I should see "Miscellaneous" in the "My new course" "table_row"
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
    Given I log in as "admin"
    And I set the following system permissions of "Course requestor" role:
      | capability            | permission |
      | moodle/course:request | Allow      |
    And I log out
    And I log in as "user1"
    And I am on course index
    And I follow "English category"
    And I press "Request a course"
    And the "Course category" select box should contain "English category"
    And I set the following fields to these values:
      | Course full name  | My new course |
      | Course short name | Mynewcourse   |
      | Supporting information | pretty please |
    And I press "Request a course"
    And I log out
    And I log in as "user2"
    And I am on course index
    And I follow "English category"
    And "Courses pending approval" "button" should not exist
    And I am on course index
    And I follow "Science category"
    And I press "Courses pending approval"
    And I should not see "Mynewcourse"
    And I press "Back to course listing"
    And I log out
    And I log in as "user3"
    And I am on course index
    And I follow "English category"
    And I press "Courses pending approval"
    And I should see "English category" in the "Mynewcourse" "table_row"
    And I click on "Approve" "button" in the "Mynewcourse" "table_row"
    And I press "Save and return"
    And I am on course index
    And I follow "English category"
    And I should see "My new course"
    And I log out
