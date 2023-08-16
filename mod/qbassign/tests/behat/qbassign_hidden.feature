@mod @mod_qbassign
Feature: When a Teacher hides an qbassignment from view for students it should consistently indicate it is hidden.

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
      | activity | qbassign                 |
      | course   | C1                     |
      | name     | Test hidden qbassignment |
      | visible  | 0                      |
    And the following "activity" exists:
      | activity | qbassign                  |
      | course   | C1                      |
      | name     | Test visible qbassignment |

  Scenario: A teacher can view a hidden qbassignment
    When I am on the "Test hidden qbassignment" Activity page logged in as teacher1
    Then I should see "Test hidden qbassignment"
    And I should see "Yes" in the "Hidden from students" "table_row"

  Scenario: A teacher can view a visible qbassignment
    Given I am on the "Test visible qbassignment" Activity page logged in as teacher1
    Then I should see "Test visible qbassignment"
    And I should see "No" in the "Hidden from students" "table_row"

  Scenario: A student cannot view a hidden qbassignment
    And I am on the "C1" Course page logged in as student1
    And I should not see "Test hidden qbassignment"
    And I should see "Test visible qbassignment"
