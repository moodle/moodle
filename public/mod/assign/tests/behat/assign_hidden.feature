@mod @mod_assign
Feature: When a Teacher hides an assignment from view for students it should consistently indicate it is hidden.

  Background: Grade multiple students on one page
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity | assign                 |
      | course   | C1                     |
      | name     | Test hidden assignment |
      | visible  | 0                      |
    And the following "activity" exists:
      | activity | assign                  |
      | course   | C1                      |
      | name     | Test visible assignment |

  Scenario: A teacher can view a hidden assignment
    When I am on the "Test hidden assignment" Activity page logged in as teacher1
    Then I should see "Test hidden assignment"
    And I should see "Yes" in the "Hidden from students" "table_row"

  Scenario: A teacher can view a visible assignment
    Given I am on the "Test visible assignment" Activity page logged in as teacher1
    Then I should see "Test visible assignment"
    And I should see "No" in the "Hidden from students" "table_row"

  Scenario: A student cannot view a hidden assignment
    And I am on the "C1" Course page logged in as student1
    And I should not see "Test hidden assignment"
    And I should see "Test visible assignment"
