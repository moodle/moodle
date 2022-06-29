@javascript @theme_boost
Feature: Context settings menu
  To navigate in boost theme I need to use the context settings menu

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Teacher can use the context settings menu
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I should see "Edit course settings"
    And I log out

  Scenario: Student cannot use the context settings menu
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And ".context-header-settings-menu [role=button]" "css_element" should not exist
    And I log out
