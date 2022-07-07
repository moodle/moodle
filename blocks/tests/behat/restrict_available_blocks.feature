@core @core_block
Feature: Allowed blocks controls
  In order to prevent the use of some blocks
  As an admin
  I need to restrict some blocks to be used in courses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Blocks can be added with the default permissions
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Course completion status" block
    And I add the "Activities" block
    Then I should see "Activities" in the "Activities" "block"
    And I should see "Course completion status" in the "Course completion status" "block"

  Scenario: Blocks can not be added when the admin restricts the permissions
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | block/activity_modules:addinstance | Prohibit |
    And I am on "Course 1" course homepage
    And I navigate to "Users > Permissions" in current page administration
    And I override the system permissions of "Teacher" role with:
      | block/completionstatus:addinstance | Prohibit |
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    Then the add block selector should not contain "Activities" block
    And the add block selector should not contain "Course completion status" block
