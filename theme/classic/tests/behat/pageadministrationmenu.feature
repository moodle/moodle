@javascript @theme_classic
Feature: Page administration menu
  To navigate in classic theme I need to use the page administration menu

  Background:
    Given the following "courses" exist:
      | fullname | shortname | newsitems |
      | Course 1 | C1        | 5 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name        | intro                   | course | idnumber | option |
      | choice   | Choice name | Test choice description | C1     | choice1  | Option 1, Option 2, Option 3 |

  Scenario: Teacher can access activity administration menus
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I should see the page administration menu
    And "Settings" "link" should exist in current page administration
    And I navigate to "Settings" in current page administration
    And I should see "Edit settings"
    And I navigate to course participants
    And I should see the page administration menu
    And I am on the "Course 1" "Enrolment methods" page
    And I should see "Enrolment methods"
    And I log out

  Scenario: Student cannot access course and activity administration menus
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see the page administration menu
    And I follow "Choice name"
    And I should not see the page administration menu
    And I log out

  Scenario: Administrator can access site administration menus and sub-menus
    And I log in as "admin"
    And I should see the page administration menu
    And I navigate to "Advanced features" in site administration
    And I should see "Enable comments"
    And I navigate to "Users > Accounts > Add a new user" in site administration
    And I should see "New password"
    And I log out
