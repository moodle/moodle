@core @core_group
Feature: Automatic deletion of groups and groupings
  In order to check the expected results occur when deleting groups and groupings in different scenarios
  As a teacher
  I need to create groups and groupings under different scenarios and check that the expected result occurs when attempting to delete them.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "groups" exist:
      | course | name               | idnumber |
      | C1     | Group (without ID) |          |
      | C1     | Group (with ID)    | An ID    |
    And the following "groupings" exist:
      | course | name                  | idnumber |
      | C1     | Grouping (without ID) |          |
      | C1     | Grouping (with ID)    | An ID    |
    And I log in as "teacher1"
    And I am on the "Course 1" "groups" page logged in as "teacher1"

  @javascript
  Scenario: Delete groups and groupings with and without ID numbers
    Given I set the field "groups" to "Group (without ID) (0)"
    And I press "Delete"
    And I press "Yes"
    Then the "groups" select box should not contain "Group (without ID) (0)"
    And I set the field "groups" to "Group (with ID) (0)"
    And I press "Delete"
    And I press "Yes"
    And the "groups" select box should not contain "Group (with ID) (0)"
    And I set the field "Participants tertiary navigation" to "Groupings"
    And I click on "Delete" "link" in the "Grouping (without ID)" "table_row"
    And I press "Yes"
    And I should not see "Grouping (without ID)"
    And I click on "Delete" "link" in the "Grouping (with ID)" "table_row"
    And I press "Yes"
    And I should not see "Grouping (with ID)"

  @javascript @skip_chrome_zerosize
  Scenario: Delete groups and groupings with and without ID numbers without the 'moodle/course:changeidnumber' capability
    Given the following "role capability" exists:
      | role                         | editingteacher |
      | moodle/course:changeidnumber | prevent        |
    And I am on the "Course 1" "groups" page
    When I set the field "groups" to "Group (with ID) (0)"
    Then the "Delete" "button" should be disabled
    And I set the field "groups" to "Group (without ID) (0)"
    And I press "Delete"
    And I press "Yes"
    And I should not see "Group (without ID)"
    And I set the field "Participants tertiary navigation" to "Groupings"
    And "Delete" "link" should not exist in the "Grouping (with ID)" "table_row"
    And I click on "Delete" "link" in the "Grouping (without ID)" "table_row"
    And I press "Yes"
    And I should not see "Grouping (without ID)"
