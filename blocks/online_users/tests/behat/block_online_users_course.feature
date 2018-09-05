@block @block_online_users
Feature: The online users block allow you to see who is currently online
  In order to enable the online users block on an course page
  As a teacher
  I can add the online users block to a course page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |

    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |

  Scenario: Add the online users on course page and see myself
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Online users" block
    Then I should see "Teacher 1" in the "Online users" "block"
    And I should see "1 online user" in the "Online users" "block"

  Scenario: Add the online users on course page and see other logged in users
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Online users" block
    And I log out
    And I log in as "student2"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Teacher 1" in the "Online users" "block"
    And I should see "Student 1" in the "Online users" "block"
    And I should not see "Student 2" in the "Online users" "block"
    And I should see "2 online users" in the "Online users" "block"
