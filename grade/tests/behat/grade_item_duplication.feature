@core @core_grades @javascript
Feature: We can duplicate grade items that already exist.
  In order to quickly create grade items that have similar settings.
  As a teacher
  I need to duplicate an existing grade item and check that its values are properly duplicated.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "grade categories" exist:
      | fullname  | course |
      | Category1 | C1     |
    And the following "activities" exist:
      | activity | course | idnumber | name        | gradecategory |
      | assign   | C1     | a1       | Assignment1 | Category1     |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "grade items" exist:
      | itemname | course | category  | idnumber | gradetype | grademax | grademin | gradepass | display | decimals | hidden | weightoverride |
      | Item1    | C1     | Category1 | 001      | Value     | 80.00    | 5.00     | 40.00     | 1       | 1        | 0      | 1              |

  Scenario: Ensure the duplicated grade item settings match the original grade item
    Given I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I should not see "Duplicate   Category1"
    And I should not see "Duplicate   Assignment1"
    When I duplicate the grade item "Item1"
    Then I should see "Item1 (copy)"
    And I click on grade item menu "Item1 (copy)" of type "gradeitem" on "setup" page
    And I choose "Edit grade item" in the open action menu
    And the field "Item name" matches value "Item1 (copy)"
    And the field "ID number" matches value ""
    And the field "Grade type" matches value "Value"
    And the field "Maximum grade" matches value "80.00"
    And the field "Minimum grade" matches value "5.00"
    And the field "Grade to pass" matches value "40.00"
    And the field "Grade display type" matches value "Real"
    And the field "Overall decimal places" matches value "1"
    And the field "Hidden" matches value "0"
    And the field "Weight adjusted" matches value "1"
