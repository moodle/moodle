@javascript @theme_iomad
Feature: Region main settings menu
  To navigate in iomad theme I need to use the region main settings menu

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

  Scenario: Student cannot use all options in the region main settings menu
    Given I log in as "student1"
    When I am on "Course 1" course homepage
    Then "#region-main-settings-menu [role=button]" "css_element" should not exist
    And I am on the "Choice name" "Choice activity" page
    And "#region-main-settings-menu [role=button]" "css_element" should not exist
