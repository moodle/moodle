@javascript @theme_boost
Feature: Region main settings menu
  To navigate in boost theme I need to use the region main settings menu

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

  Scenario: Teacher can use the region main settings menu
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "#region-main-settings-menu [role=button]" "css_element" should not exist
    And I follow "Choice name"
    And I click on "#region-main-settings-menu [role=button]" "css_element"
    And I choose "Edit settings" in the open action menu
    And I should see "Updating: Choice"
    And I navigate to course participants
    And I click on "#region-main-settings-menu [role=button]" "css_element"
    And I choose "Enrolment methods" in the open action menu
    And I should see "Enrolment methods"
    And I log out

  Scenario: Student cannot use all options in the region main settings menu
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And "#region-main-settings-menu [role=button]" "css_element" should not exist
    And I follow "Choice name"
    And "#region-main-settings-menu [role=button]" "css_element" should not exist
    And I log out
